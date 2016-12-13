<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 13.12.16.
 * Time: 19.22
 */

namespace App\Handlers\File;


use AuthBucket\OAuth2\Exception\InvalidRequestException;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotBlank;

class CoverPictureHandler extends AbstractFileHandler
{
    public function upload(Application $app, Request $request)
    {
        $user_id = $request->request->get('user_id');
        $cover = $request->files->get('cover');

        $errors = $this->validator->validate($cover, [
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

        // TODO: Send image to Amazon S3 Storage.

    }


}