<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 29.11.16.
 * Time: 22.07
 */

namespace App\Controllers\API;


use App\Controllers\AbstractController;
use App\Handlers\AbstractHandler;
use App\Handlers\MateyUser\UserHandlerFactoryInterface;
use App\Handlers\Profile\ProfileHandlerFactoryInterface;
use App\MateyModels\ModelManagerFactoryInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
{

    protected $userHandlerFactory;

    public function __construct(
        UserHandlerFactoryInterface $userHandlerFactory
    ) {
        $this->userHandlerFactory = $userHandlerFactory;
    }

    public function getUserAction(Application $app, Request $request, $userId) {
        if($userId == "me") $userId = AbstractHandler::getTokenUserId($request);

        $userData = $this->userHandlerFactory
            ->getUserHandler('user')
            ->handleGetUser($app, $request, $userId);

        $finalResult['data'] = $userData;

        return new JsonResponse($finalResult, 200);
    }

    public function getUserProfileAction(Application $app, Request $request, $userId) {
        if($userId == "me") $userId = AbstractHandler::getTokenUserId($request);

        return $this->userHandlerFactory
            ->getUserHandler('user')
            ->handleGetUser($app, $request, $userId);
    }

    public function followAction(Application $app, Request $request, $id) {
        return $this->userHandlerFactory
            ->getUserHandler('user')
            ->handleFollow($app, $request, $id);
    }

    public function getFollowersAction(Application $app, Request $request, $userId) {
        return $this->userHandlerFactory
            ->getUserHandler('user')
            ->handleGetConnections($app, $request, $userId, 'followers');
    }

    public function getFollowingAction(Application $app, Request $request, $userId) {
        return $this->userHandlerFactory
            ->getUserHandler('user')
            ->handleGetConnections($app, $request, $userId, 'following');
    }

    public function uploadProfilePictureAction (Application $app, Request $request) {
        $fileHandler = $app['matey.file_handler.factory'];
        return $fileHandler
            ->getFileHandler('profile_picture')
            ->upload($app, $request);
    }

    public function uploadCoverPictureAction (Application $app, Request $request) {
        $fileHandler = $app['matey.file_handler.factory'];
        return $fileHandler
            ->getFileHandler('cover_picture')
            ->upload($app, $request);
    }


}