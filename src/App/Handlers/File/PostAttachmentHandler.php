<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 4.3.17.
 * Time: 00.20
 */

namespace App\Handlers\File;


use App\Constants\Messages\ResponseMessages;
use App\MateyModels\Post;
use App\MateyModels\Reply;
use App\Paths\Paths;
use App\Upload\S3Storage;
use AuthBucket\OAuth2\Exception\InvalidRequestException;
use AuthBucket\OAuth2\Exception\ServerErrorException;
use AuthBucket\OAuth2\Model\ModelInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotBlank;

class PostAttachmentHandler extends AbstractFileHandler
{

    const LOCATION_POSTS = 'posts';
    const LOCATION_REPLIES = 'replies';

    public function upload(Application $app, Request $request, $id = null, $location = null)
    {
        $files = array();
        for($iterator = $request->files->getIterator();
            $iterator->valid();
            $iterator->next()) {
            $files[] = $request->files->get($iterator->key());
        }

        if(count($files) > 5) throw new InvalidRequestException(
            array('error' => ResponseMessages::TOO_MUCH_FILES)
        );

        $uploads = array();
        $fieldId = 1;
        foreach($files as $file) {

            $this->validateValue($file, [
                new NotBlank(),
                new Image(array(
                    'allowLandscape' => true,
                    'allowPortrait' => true,
                    'mimeTypes' => ['image/jpeg', 'image/png'],
                    'maxSize' => '1M'
                ))
            ]);

            $uploads[] = array(
                "file" => file_get_contents($file->getRealPath()),
                'name' => PostAttachmentHandler::generateAttachPrefix($id, $fieldId++, $location),
                'mime' => $file->getMimeType(),
                'extension' => $file->guessExtension(),
                'filename' => $file->getClientOriginalName()
            );
        }

        $cloudStorage = new S3Storage($uploads);
        $cloudStorage->upload();

        return new JsonResponse(null, 200);

    }

    public static function generateAttachPrefix ($postId, $attachId, $location) {
        return $location."/".$postId."/attachs/".$attachId;
    }

    public static function generateAttachUrl ($postId, $attachId, $location) {
        return Paths::STORAGE_BASE."/".Paths::BUCKET_MATEY."/".PostAttachmentHandler::generateAttachPrefix($postId, $attachId, $location);
    }

    public static function getAttachUrls (ModelInterface $model, $location = self::LOCATION_POSTS) {
        if(!($model instanceof Post) && !($model instanceof Reply)) throw new ServerErrorException();

        $arr = array();
        for($i=1; $i<=$model->getAttachsNum(); $i++) {
            $arr[] = array('file_url' => PostAttachmentHandler::generateAttachUrl($model->getId(), $i, $location));
        }
        return $arr;
    }

}