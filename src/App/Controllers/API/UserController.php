<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 29.11.16.
 * Time: 22.07
 */

namespace App\Controllers\API;


use App\Controllers\AbstractController;
use App\Handlers\MateyUser\UserHandlerFactoryInterface;
use App\Handlers\Profile\ProfileHandlerFactoryInterface;
use App\MateyModels\ModelManagerFactoryInterface;
use Silex\Application;
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

    public function getUserAction(Request $request, $userId) {
        if($userId == "me") $userId = $request->request->get('user_id');

        return $this->userHandlerFactory
            ->getUserHandler('user')
            ->getUser($request, $userId);
    }

    public function followAction(Application $app, Request $request, $id) {
        return $this->userHandlerFactory
            ->getUserHandler('user')
            ->follow($app, $request, $id);
    }

    public function getFollowersAction(Application $app, Request $request, $userId) {
        return $this->userHandlerFactory
            ->getUserHandler('user')
            ->getConnections($app, $request, $userId, 'followers');
    }

    public function getFollowingAction(Application $app, Request $request, $userId) {
        return $this->userHandlerFactory
            ->getUserHandler('user')
            ->getConnections($app, $request, $userId, 'following');
    }

    public function getFeedAction(Application $app, Request $request) {
        return $this->userHandlerFactory
            ->getUserHandler('user')
            ->getFeed($app, $request);
    }


}