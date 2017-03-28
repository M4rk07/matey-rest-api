<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 28.3.17.
 * Time: 14.58
 */

namespace App\Handlers\File;


use App\MateyModels\User;
use App\Paths\Paths;
use App\Upload\S3Storage;
use AuthBucket\OAuth2\Exception\InvalidRequestException;
use Doctrine\Common\Proxy\Exception\InvalidArgumentException;
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotBlank;

class CoverPictureHandler extends AbstractFileHandler
{

    const SMALL = '100x100';
    const MEDIUM = '200x200';
    const LARGE = '480x480';
    const ORIGINAL = 'original';

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
                'name' => ProfilePictureHandler::generatePicturePrefix($userId, self::SMALL),
                'mime' => $picture->getMimeType(),
                'extension' => $picture->guessExtension(),
                'filename' => $picture->getClientOriginalName()
            ),
            array(
                'file' => $picture200x200,
                'name' => ProfilePictureHandler::generatePicturePrefix($userId, self::MEDIUM),
                'mime' => $picture->getMimeType(),
                'extension' => $picture->guessExtension(),
                'filename' => $picture->getClientOriginalName()
            ),
            array(
                'file' => $picture480x480,
                'name' => ProfilePictureHandler::generatePicturePrefix($userId, self::LARGE),
                'mime' => $picture->getMimeType(),
                'extension' => $picture->guessExtension(),
                'filename' => $picture->getClientOriginalName()
            ),
            array(
                'file' => file_get_contents($originalPicture),
                'name' => ProfilePictureHandler::generatePicturePrefix($userId, self::ORIGINAL),
                'mime' => $picture->getMimeType(),
                'extension' => $picture->guessExtension(),
                'filename' => $picture->getClientOriginalName()
            )
        );

        $cloudStorage = new S3Storage($uploads);
        $cloudStorage->upload();

        $userManager = $this->modelManagerFactory->getModelManager('user');
        $user = $userManager->getModel();

        $user->setSilhouette(0);

        $userManager->updateModel($user, array(
            'user_id' => $userId
        ));

        $user->setUserId($userId);

        return new JsonResponse(null, 201, array(
            'Location' => self::getPictureUrl($user)
        ));
    }

    public static function generatePicturePrefix ($userId, $dimension = self::SMALL) {
        return "users/pictures/".$dimension."/".$userId;
    }

    public static function generatePictureUrl ($userId, $dimension = self::SMALL) {
        return Paths::STORAGE_BASE."/".Paths::BUCKET_MATEY."/".ProfilePictureHandler::generatePicturePrefix($userId, $dimension);
    }

    public static function getCoverUrl(User $user, $dimension = self::SMALL) {
        return "https://mostaql.hsoubcdn.com/uploads/89566-1466668782-background.png";
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