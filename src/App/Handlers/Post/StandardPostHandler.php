<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 3.3.17.
 * Time: 16.10
 */

namespace App\Handlers\Post;

use AuthBucket\OAuth2\Exception\InvalidRequestException;
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class StandardPostHandler extends AbstractPostHandler
{

    public function createPost(Application $app, Request $request) {
        $userId = $request->request->get('user_id');
        // Getting json data in relation to Content-Type
        $contentType = $request->headers->get('Content-Type');
        $jsonData = $this->getJsonDataFromRequest($request, $contentType);

        $postManager = $this->modelManagerFactory->getModelManager('post');
        $postClass = $postManager->getClassName();
        $post = new $postClass;

        $post->setTitle($jsonData->title)
            ->setAttachsNum($request->files->count())
            ->setUserId($userId);

        if(isset($jsonData->text)) $post->setText($jsonData->text);
        isset($jsonData->locations) ? $post->setLocationsNum(count($jsonData->locations)) : $post->setLocationsNum(0);
        isset($jsonData->group_id) ? $post->setGroupId($jsonData->group_id) : $post->setGroupId(1);

        $post = $postManager->createModel($post);

        // UPLOAD FILES TO S3
        if(strpos($contentType, 'multipart/form-data') === 0) {
            $app['matey.file_handler.factory']->getFileHandler('attachment')->upload($app, $request, $post->getId());
        }

        return new JsonResponse(null, 200);

    }

    public function getJsonDataFromRequest (Request $request, $contentType) {

        if($contentType == 'application/json') {
            $jsonData = $request->getContent();
            $jsonData = json_decode($jsonData);
        } else if(strpos($contentType, 'multipart/form-data') === 0) {
            $jsonData = $request->request->get('json_data');
            $jsonData = json_decode($jsonData);
        }

        if(empty($jsonData)) return new InvalidRequestException();

        return $jsonData;

    }

}