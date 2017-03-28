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
        $picture = $request->files->get('cover');

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

        $uploads = array(
            array(
                'file' => file_get_contents($originalPicture),
                'name' => CoverPictureHandler::generatePicturePrefix($userId, self::ORIGINAL),
                'mime' => $picture->getMimeType(),
                'extension' => $picture->guessExtension(),
                'filename' => $picture->getClientOriginalName()
            )
        );

        $cloudStorage = new S3Storage($uploads);
        $cloudStorage->upload();

        $userManager = $this->modelManagerFactory->getModelManager('user');
        $user = $userManager->getModel();


        // TODO: Implement cover silhouette method and mysql field
        $user->setCoverSilhouette(0);

        $userManager->updateModel($user, array(
            'user_id' => $userId
        ));

        $user->setUserId($userId);

        return new JsonResponse(null, 201, array(
            'Location' => self::getPictureUrl($user)
        ));
    }

    public static function generatePicturePrefix ($userId, $dimension = self::SMALL) {
        return "users/covers/".$dimension."/".$userId;
    }

    public static function generatePictureUrl ($userId, $dimension = self::SMALL) {
        return Paths::STORAGE_BASE."/".Paths::BUCKET_MATEY."/".CoverPictureHandler::generatePicturePrefix($userId, $dimension);
    }

    public static function getCoverUrl(User $user, $dimension = self::SMALL) {
        return "https://mostaql.hsoubcdn.com/uploads/89566-1466668782-background.png";
    }

}