<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 4.3.17.
 * Time: 00.20
 */

namespace App\Handlers\File;


use App\Upload\S3Storage;
use AuthBucket\OAuth2\Exception\InvalidRequestException;
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotBlank;

class PostAttachmentHandler extends AbstractFileHandler
{

    public function upload(Application $app, Request $request, $id = null)
    {
        $files = array();
        for($iterator = $request->files->getIterator();
            $iterator->valid();
            $iterator->next()) {
            $files[] = $request->files->get($iterator->key());
        }

        $uploads = array();
        $fileId = 1;
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
                'name' => 'posts/'.$id.'/'.$fileId++.'.jpg'
            );

        }

        $cloudStorage = new S3Storage($uploads);
        $cloudStorage->upload();

        return new JsonResponse(null, 200);

    }

}