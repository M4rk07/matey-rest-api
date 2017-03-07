<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 3.3.17.
 * Time: 23.12
 */

namespace App\Controllers\API;


use App\Controllers\AbstractController;
use App\Handlers\Post\PostHandlerFactoryInterface;
use App\MateyModels\ModelManagerFactoryInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PostController extends AbstractController
{
    protected $postHandlerFactory;

    public function __construct(
        PostHandlerFactoryInterface $postHandlerFactory
    ) {
        $this->postHandlerFactory = $postHandlerFactory;
    }

    public function createPostAction (Application $app, Request $request) {
        return $this->postHandlerFactory
            ->getPostHandler('standard')
            ->createPost($app, $request);
    }

}