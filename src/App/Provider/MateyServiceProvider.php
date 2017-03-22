<?php

namespace App\Provider;
use App\Controllers\API\AccountController;
use App\Controllers\API\ConnectionController;
use App\Controllers\API\DeviceController;
use App\Controllers\API\FeedController;
use App\Controllers\API\FileController;
use App\Controllers\API\GroupController;
use App\Controllers\API\PostController;
use App\Controllers\API\ProfileController;
use App\Controllers\API\ProfilePictureController;
use App\Controllers\API\ReplyController;
use App\Controllers\API\RereplyController;
use App\Controllers\API\SearchController;
use App\Controllers\API\TestDataController;
use App\Controllers\API\UserController;
use App\Controllers\RegistrationController;
use App\Handlers\Account\AccountHandlerFactory;
use App\Handlers\Bulletin\Post\PostHandler;
use App\Handlers\Bulletin\Post\StandardPostHandler;
use App\Handlers\Bulletin\Reply\ReplyHandler;
use App\Handlers\Bulletin\Rereply\RereplyHandler;
use App\Handlers\Bulletin\StandardReply\StandardReplyHandler;
use App\Handlers\Connections\ConnectionHandlerFactory;
use App\Handlers\Device\DeviceHandlerFactory;
use App\Handlers\Feed\FeedHandler;
use App\Handlers\File\FileHandlerFactory;
use App\Handlers\Group\GroupHandler;
use App\Handlers\Group\GroupHandlerFactory;
use App\Handlers\MateyUser\UserHandlerFactory;
use App\Handlers\MergeAccount\MergeAccountHandlerFactory;
use App\Handlers\Post\PostHandlerFactory;
use App\Handlers\Profile\ProfileHandlerFactory;
use App\Handlers\ProfilePicture\ProfilePictureHandler;
use App\Handlers\Search\SearchHandler;
use App\Handlers\TestingData\TestingDataHandler;
use App\MateyModels\Group;
use App\MateyModels\ModelManagerFactory;
use App\RoutesLoader;
use App\Services\SearchService;
use App\ServicesLoader;
use App\Handlers\Registration\RegistrationHandlerFactory;
use App\Validators\PositiveInteger;
use App\Validators\UnsignedInteger;
use AuthBucket\OAuth2\Exception\InvalidRequestException;
use Resource\Config\ConfigProvider;
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
use Symfony\Component\Validator\Constraints\NotBlank;

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

        $configReader = new ConfigProvider();
        $app['matey.models_config'] = $configReader->getModelsConfig();

        $app['matey.model_manager.factory'] = $app->share(function ($app) {
            return new ModelManagerFactory(
                $app['matey.models_config'],
                $app['db'],
                $app['predis']
            );
        });

        $app['matey.search_service'] = $app->share(function ($app) {
           return new SearchService($app['predis']);
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
            'group_picture' => 'App\\Handlers\\File\\GroupPictureHandler',
            'post_attachment' => 'App\\Handlers\\File\\PostAttachmentHandler',
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

        $app['matey.group_handler'] = $app->share(function($app) {
            return new GroupHandler(
                $app['validator'],
                $app['matey.model_manager.factory']
            );
        });

        $app['matey.post_handler'] = $app->share(function($app) {
            return new PostHandler(
                $app['validator'],
                $app['matey.model_manager.factory']
            );
        });

        $app['matey.reply_handler'] = $app->share(function($app) {
            return new StandardReplyHandler(
                $app['validator'],
                $app['matey.model_manager.factory']
            );
        });

        $app['matey.rereply_handler'] = $app->share(function($app) {
            return new RereplyHandler(
                $app['validator'],
                $app['matey.model_manager.factory']
            );
        });

        $app['matey.search_handler'] = $app->share(function($app) {
            return new SearchHandler(
                $app['validator'],
                $app['matey.model_manager.factory']
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
                $app['matey.account_handler.factory']
            );
        });

        $app['matey.device_controller'] = $app->share(function () use ($app) {
            return new DeviceController(
                $app['matey.device_handler.factory']
            );
        });

        $app['matey.user_controller'] = $app->share(function () use ($app) {
            return new UserController(
                $app['matey.user_handler.factory']
            );
        });

        $app['matey.group_controller'] = $app->share(function () use ($app) {
            return new GroupController(
                $app['matey.group_handler']
            );
        });

        $app['matey.post_controller'] = $app->share(function () use ($app) {
            return new PostController(
                $app['matey.post_handler']
            );
        });

        $app['matey.reply_controller'] = $app->share(function () use ($app) {
            return new ReplyController(
                $app['matey.reply_handler']
            );
        });

        $app['matey.rereply_controller'] = $app->share(function () use ($app) {
            return new RereplyController(
                $app['matey.rereply_handler']
            );
        });

        $app['matey.search_controller'] = $app->share(function () use ($app) {
            return new SearchController(
                $app['matey.search_handler']
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