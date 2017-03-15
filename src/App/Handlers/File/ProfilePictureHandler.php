<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 12.12.16.
 * Time: 14.18
 */

namespace App\Handlers\File;


use App\Paths\Paths;
use App\Upload\CloudStorageUpload;
use App\Upload\S3Storage;
use AuthBucket\OAuth2\Exception\InvalidRequestException;
use Doctrine\Common\Proxy\Exception\InvalidArgumentException;
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProfilePictureHandler extends AbstractFileHandler
{

    public function upload (Application $app, Request $request)
    {
        $userId = $request->request->get('user_id');
        $picture = $request->files->get('picture');

        $errors = $this->validator->validate($picture, [
            new NotBlank(),
            new Image(array(
                'allowLandscape' => false,
                'allowPortrait' => false,
                'mimeTypes' => ['image/jpeg', 'image/png'],
                'maxSize' => '500k'
            ))
        ]);
        if (count($errors) > 0) {
            throw new InvalidRequestException([
                'error_description' => $errors->get(0)->getMessage(),
            ]);
        }

        $originalPicture = $picture->getRealPath();

            ob_start(); // start a new output buffer
                imagejpeg( $this->resizeImage($originalPicture, 100, 100), NULL, 90);
                $picture100x100 = ob_get_contents();
            ob_end_clean(); // stop this output buffer
            ob_start(); // start a new output buffer
                imagejpeg( $this->resizeImage($originalPicture, 200, 200), NULL, 90);
                $picture200x200 = ob_get_contents();
            ob_end_clean(); // stop this output buffer
            ob_start(); // start a new output buffer
                imagejpeg( $this->resizeImage($originalPicture, 480, 480), NULL, 90);
                $picture480x480 = ob_get_contents();
            ob_end_clean(); // stop this output buffer

        $uploads = array(
            array(
                'file' => $picture100x100,
                'name' => 'pictures/100x100/'.$userId,
                'mime' => $picture->getMimeType(),
                'extension' => $picture->guessExtension(),
                'filename' => $picture->getClientOriginalName()
            ),
            array(
                'file' => $picture200x200,
                'name' => 'pictures/200x200/'.$userId,
                'mime' => $picture->getMimeType(),
                'extension' => $picture->guessExtension(),
                'filename' => $picture->getClientOriginalName()
            ),
            array(
                'file' => $picture480x480,
                'name' => 'pictures/480x480/'.$userId,
                'mime' => $picture->getMimeType(),
                'extension' => $picture->guessExtension(),
                'filename' => $picture->getClientOriginalName()
            ),
            array(
                'file' => file_get_contents($originalPicture),
                'name' => 'pictures/originals/'.$userId,
                'mime' => $picture->getMimeType(),
                'extension' => $picture->guessExtension(),
                'filename' => $picture->getClientOriginalName()
            )
        );

        $cloudStorage = new S3Storage($uploads);
        $cloudStorage->upload();

        $userManager = $this->modelManagerFactory->getModelManager('user');
        $userClass = $userManager->getClassName();
        $user = new $userClass();

        $user->setSilhouette(0);

        $userManager->updateModel($user, array(
            'user_id' => $userId
        ));

        $user->setUserId($userId);

        return new JsonResponse(null, 201, array(
            'Location' => $user->getProfilePicture('original')
        ));
    }



    function compress($source, $destination, $quality) {

        $info = getimagesize($source);

        if ($info['mime'] == 'image/jpeg')
            $image = imagecreatefromjpeg($source);

        elseif ($info['mime'] == 'image/png')
            $image = imagecreatefrompng($source);

        else throw new InvalidArgumentException();

        imagejpeg($image, $destination, $quality);

        return $destination;
    }

}