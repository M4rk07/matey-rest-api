<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 3.3.17.
 * Time: 16.10
 */

namespace App\Handlers\Post;

use AuthBucket\OAuth2\Exception\InvalidRequestException;
use Symfony\Component\HttpFoundation\Request;

class StandardPostHandler extends AbstractPostHandler
{

    public function createPost(Request $request) {
        $contentType = $request->headers->get('Content-Type');
        if($contentType == 'application/json') {
            $postData = $request->getContent();
            $postData = json_decode($postData);
        } else if($contentType == 'multipart/form-data') {
            $postData = $request->files->get('json_data');
            $postData = json_decode($postData);
        }

        if(empty($postData)) return new InvalidRequestException();

        $title = $postData->title;
        $text = $postData->text;
        $locations = $postData->locations;

    }

}