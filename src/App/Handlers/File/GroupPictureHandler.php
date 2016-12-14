<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 14.12.16.
 * Time: 23.42
 */

namespace App\Handlers\File;


use App\Upload\CloudStorageUpload;
use App\Validators\GroupId;
use AuthBucket\OAuth2\Exception\InvalidRequestException;
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotBlank;

class GroupPictureHandler extends AbstractFileHandler
{
    public function upload(Application $app, Request $request, $groupId = null)
    {
        $userId = $request->request->get('user_id');
        $picture = $request->files->get('group_picture');

        // TODO: Proveriti da li je korisnik autorizovan da izvrsi upload slike.

        $errors = $this->validator->validate($groupId, [
            new NotBlank(),
            new GroupId()
        ]);
        if (count($errors) > 0) {
            throw new InvalidRequestException([
                'error_description' => $errors->get(0)->getMessage(),
            ]);
        }

        $errors = $this->validator->validate($picture, [
            new NotBlank(),
            new Image(array(
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
                'file' => file_get_contents($originalPicture),
                'name' => 'groups/originals/'.$userId.'.jpg'
            )
        );

        $cloudStorage = new CloudStorageUpload($uploads);
        $cloudStorage->upload();

        $groupManager = $this->modelManagerFactory->getModelManager('group');
        $groupClass = $groupManager->getClassName();
        $group = new $groupClass();

        $group->setSilhouette(0);

        $groupManager->updateModel($group, array(
            'group_id' => $userId
        ));

        $group->setId($groupId);

        return new JsonResponse(null, 201, array(
            'Location' => $group->getGroupPicture('original')
        ));
    }


}