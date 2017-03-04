<?php

namespace App\Provider;
use App\Controllers\API\AccountController;
use App\Controllers\API\ConnectionController;
use App\Controllers\API\DeviceController;
use App\Controllers\API\FileController;
use App\Controllers\API\GroupController;
use App\Controllers\API\PostController;
use App\Controllers\API\ProfileController;
use App\Controllers\API\ProfilePictureController;
use App\Controllers\API\TestDataController;
use App\Controllers\API\UserController;
use App\Controllers\RegistrationController;
use App\Handlers\Account\AccountHandlerFactory;
use App\Handlers\Connections\ConnectionHandlerFactory;
use App\Handlers\Device\DeviceHandlerFactory;
use App\Handlers\File\FileHandlerFactory;
use App\Handlers\Group\GroupHandlerFactory;
use App\Handlers\MateyUser\UserHandlerFactory;
use App\Handlers\MergeAccount\MergeAccountHandlerFactory;
use App\Handlers\Post\PostHandlerFactory;
use App\Handlers\Profile\ProfileHandlerFactory;
use App\Handlers\ProfilePicture\ProfilePictureHandler;
use App\Handlers\TestingData\TestingDataHandler;
use App\MateyModels\ModelManagerFactory;
use App\RoutesLoader;
use App\ServicesLoader;
use App\Handlers\Registration\RegistrationHandlerFactory;
use Silex\Application;
use Silex\Provider\HttpCacheServiceProvider;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\MonologServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\ServiceProviderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Carbon\Carbon;

/**
 * Created by PhpStorm.
 * User: marko
 * Date: 8.10.16.
 * Time: 22.39
 */
class MateyServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {

        $app['matey.model'] = [
            'user' => 'App\\MateyModels\\User',
            'facebookInfo' => 'App\\MateyModels\\FacebookInfo',
            'oauth2User' => 'App\\MateyModels\\OAuth2User',
            'device' => 'App\\MateyModels\\Device',
            'login' => 'App\\MateyModels\\Login',
            'follow' => 'App\\MateyModels\\Follow',
            'group' => 'App\\MateyModels\\Group',
            'group_admin' => 'App\\MateyModels\\GroupAdmin',
            'group_favorite' => 'App\\MateyModels\\GroupFavorite',
            'location' => 'App\\MateyModels\\Location',
            'activity' => 'App\\MateyModels\\Activity',
            'approve' => 'App\\MateyModels\\Approve',
            'bookmark' => 'App\\MateyModels\\Bookmark',
            'boost' => 'App\\MateyModels\\Boost',
            'post' => 'App\\MateyModels\\Post',
            'reply' => 'App\\MateyModels\\Reply',
            'rereply' => 'App\\MateyModels\\Rereply',
            'share' => 'App\\MateyModels\\Share',
        ];

        $app['matey.model_manager.factory'] = $app->share(function ($app) {
            return new ModelManagerFactory(
                $app['matey.model'],
                $app['db'],
                $app['predis']
            );
        });

                    // HANDLERS //

        $app['matey.handlers.account'] = [
            'standard' => 'App\\Handlers\\Account\\StandardAccountHandler',
            'facebook' => 'App\\Handlers\\Account\\FacebookAccountHandler'
        ];

        $app['matey.handlers.device'] = [
            'android' => 'App\\Handlers\\Device\\AndroidDeviceHandler'
        ];

        $app['matey.handlers.user'] = [
            'user' => 'App\\Handlers\\MateyUser\\UserHandler'
        ];

        $app['matey.handlers.file'] = [
            'profile_picture' => 'App\\Handlers\\File\\ProfilePictureHandler',
            'cover_picture' => 'App\\Handlers\\File\\CoverPictureHandler',
            'group_picture' => 'App\\Handlers\\File\\GroupPictureHandler',
            'post_attachment' => 'App\\Handlers\\File\\PostAttachmentHandler',
        ];

        $app['matey.handlers.group'] = [
            'standard' => 'App\\Handlers\\Group\\StandardGroupHandler'
        ];

        $app['matey.handlers.post'] = [
            'standard' => 'App\\Handlers\\Post\\StandardPostHandler'
        ];

                    // HANDLERS FACTORIES //

        $app['matey.account_handler.factory'] = $app->share(function($app) {
           return new AccountHandlerFactory(
                $app['validator'],
                $app['matey.model_manager.factory'],
                $app['matey.handlers.account']
           );
        });

        $app['matey.device_handler.factory'] = $app->share(function($app) {
            return new DeviceHandlerFactory(
                $app['validator'],
                $app['matey.model_manager.factory'],
                $app['matey.handlers.device']
            );
        });

        $app['matey.user_handler.factory'] = $app->share(function($app) {
            return new UserHandlerFactory(
                $app['validator'],
                $app['matey.model_manager.factory'],
                $app['matey.handlers.user']
            );
        });

        $app['matey.file_handler.factory'] = $app->share(function($app) {
            return new FileHandlerFactory(
                $app['validator'],
                $app['matey.model_manager.factory'],
                $app['matey.handlers.file']
            );
        });

        $app['matey.group_handler.factory'] = $app->share(function($app) {
            return new GroupHandlerFactory(
                $app['validator'],
                $app['matey.model_manager.factory'],
                $app['matey.handlers.group']
            );
        });

        $app['matey.post_handler.factory'] = $app->share(function($app) {
            return new PostHandlerFactory(
                $app['validator'],
                $app['matey.model_manager.factory'],
                $app['matey.handlers.post']
            );
        });

        $app['matey.testingdata_handler'] = $app->share(function($app) {
            return new TestingDataHandler(
                $app['validator'],
                $app['matey.model_manager.factory'],
                $app['db']
            );
        });

                    // CONTROLLERS //

        $app['matey.account_controller'] = $app->share(function () use ($app) {
            return new AccountController(
                $app['validator'],
                $app['matey.model_manager.factory'],
                $app['matey.account_handler.factory']
            );
        });

        $app['matey.device_controller'] = $app->share(function () use ($app) {
            return new DeviceController(
                $app['validator'],
                $app['matey.model_manager.factory'],
                $app['matey.device_handler.factory']
            );
        });

        $app['matey.user_controller'] = $app->share(function () use ($app) {
            return new UserController(
                $app['validator'],
                $app['matey.model_manager.factory'],
                $app['matey.user_handler.factory']
            );
        });

        $app['matey.file_controller'] = $app->share(function () use ($app) {
            return new FileController(
                $app['validator'],
                $app['matey.model_manager.factory'],
                $app['matey.file_handler.factory']
            );
        });

        $app['matey.group_controller'] = $app->share(function () use ($app) {
            return new GroupController(
                $app['validator'],
                $app['matey.model_manager.factory'],
                $app['matey.group_handler.factory']
            );
        });

        $app['matey.post_controller'] = $app->share(function () use ($app) {
            return new PostController(
                $app['validator'],
                $app['matey.model_manager.factory'],
                $app['matey.post_handler.factory']
            );
        });

        $app['matey.testingdata_controller'] = $app->share(function () use ($app) {
            return new \App\Controllers\TEST\TestDataController(
                $app['validator'],
                $app['matey.model_manager.factory'],
                $app['matey.testingdata_handler']
            );
        });


    }

    public function boot(Application $app)
    {
        // TODO: Implement boot() method.
    }


}