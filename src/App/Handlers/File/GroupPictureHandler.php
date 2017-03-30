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
        $userId = $request->query->get('token-user-id');
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
        //$groupAdminManager = $this->modelManagerFactory->getModelManager('groupAdmin');

        //$groupAdmin = $groupAdminManager->readModelOneBy(array(
        //    'group_id' =>$groupId,
        //    'user_id' => $userId
        //));

        //if(empty($groupAdmin)) throw new UnauthorizedClientException();

        $originalPicture = $picture->getRealPath();

        $uploads = array(
            array(
                'file' => file_get_contents($originalPicture),
                'name' => GroupPictureHandler::generatePicturePrefix($groupId, self::ORIGINAL),
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

    public static function generatePicturePrefix ($groupId, $dimension = self::ORIGINAL) {
        return "groups/pictures/".$dimension."/".$groupId;
    }

    public static function generatePictureUrl ($groupId, $dimension = self::ORIGINAL) {
        return Paths::STORAGE_BASE."/".Paths::BUCKET_MATEY."/".GroupPictureHandler::generatePicturePrefix($groupId, $dimension);
    }

    public static function getPictureUrl(Group $group, $dimension = self::ORIGINAL) {
        if($group->isSilhouette() == 1 || $group->isSilhouette() === null) return "https://www.linkedin.com/mpr/mpr/AAEAAQAAAAAAAArhAAAAJDY4MjFlMDNiLTNlYjUtNGQ1Mi05NmM0LTEyMWJlMjMzNGRhYg.jpg";
        return GroupPictureHandler::generatePictureUrl($group->getGroupId(), $dimension);
    }


}