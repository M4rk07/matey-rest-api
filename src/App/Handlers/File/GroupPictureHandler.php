<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 14.12.16.
 * Time: 23.42
 */

namespace App\Handlers\File;


use App\MateyModels\Group;
use App\Paths\Paths;
use App\Upload\CloudStorageUpload;
use App\Upload\S3Storage;
use App\Validators\GroupId;
use AuthBucket\OAuth2\Exception\InvalidRequestException;
use AuthBucket\OAuth2\Exception\UnauthorizedClientException;
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotBlank;

class GroupPictureHandler extends AbstractFileHandler
{
    const SMALL = '100x100';
    const MEDIUM = '200x200';
    const LARGE = '480x480';
    const ORIGINAL = 'original';

    public function upload(Application $app, Request $request, $groupId = null)
    {
        $userId = $request->request->get('user_id');
        $picture = $request->files->get('group_picture');

        if(count($request->files) != 1) throw new InvalidRequestException();

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

        /*
         * Check if user is owner of the group or not
         */
        $groupAdminManager = $this->modelManagerFactory->getModelManager('groupAdmin');

        $groupAdmin = $groupAdminManager->readModelOneBy(array(
            'group_id' =>$groupId,
            'user_id' => $userId
        ));

        if(empty($groupAdmin)) throw new UnauthorizedClientException();

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
                'name' => ProfilePictureHandler::generatePicturePrefix($groupId, self::SMALL),
                'mime' => $picture->getMimeType(),
                'extension' => $picture->guessExtension(),
                'filename' => $picture->getClientOriginalName()
            ),
            array(
                'file' => $picture200x200,
                'name' => ProfilePictureHandler::generatePicturePrefix($groupId, self::MEDIUM),
                'mime' => $picture->getMimeType(),
                'extension' => $picture->guessExtension(),
                'filename' => $picture->getClientOriginalName()
            ),
            array(
                'file' => $picture480x480,
                'name' => ProfilePictureHandler::generatePicturePrefix($groupId, self::LARGE),
                'mime' => $picture->getMimeType(),
                'extension' => $picture->guessExtension(),
                'filename' => $picture->getClientOriginalName()
            ),
            array(
                'file' => file_get_contents($originalPicture),
                'name' => ProfilePictureHandler::generatePicturePrefix($groupId, self::ORIGINAL),
                'mime' => $picture->getMimeType(),
                'extension' => $picture->guessExtension(),
                'filename' => $picture->getClientOriginalName()
            )
        );

        $cloudStorage = new S3Storage($uploads);
        $cloudStorage->upload();

        $groupManager = $this->modelManagerFactory->getModelManager('group');
        $group = $groupManager->getModel();

        $group->setSilhouette(0);

        $groupManager->updateModel($group, array(
            'group_id' => $userId
        ));

        $group->setId($groupId);

        return new JsonResponse(null, 201, array(
            'Location' => self::getPictureUrl($group)
        ));
    }

    public static function generatePicturePrefix ($groupId, $dimension = self::SMALL) {
        return "groups/pictures/".$dimension."/".$groupId;
    }

    public static function generatePictureUrl ($groupId, $dimension = self::SMALL) {
        return Paths::STORAGE_BASE."/".Paths::BUCKET_MATEY."/".GroupPictureHandler::generatePicturePrefix($groupId, $dimension);
    }

    public static function getPictureUrl(Group $group, $dimension = self::SMALL) {
        if($group->isSilhouette() == 0) return "https://tctechcrunch2011.files.wordpress.com/2010/10/pirate.jpg";
        return GroupPictureHandler::generatePictureUrl($group->getGroupId(), $dimension);
    }


}