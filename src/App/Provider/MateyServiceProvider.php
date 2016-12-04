<?php

namespace App\Provider;
use App\Controllers\API\AccountController;
use App\Controllers\API\DeviceController;
use App\Controllers\API\ProfileController;
use App\Controllers\API\UserController;
use App\Controllers\RegistrationController;
use App\Handlers\Account\AccountHandlerFactory;
use App\Handlers\Device\DeviceHandlerFactory;
use App\Handlers\MateyUser\UserHandlerFactory;
use App\Handlers\MergeAccount\MergeAccountHandlerFactory;
use App\Handlers\Profile\ProfileHandlerFactory;
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
            'follow' => 'App\\MateyModels\\Follow'
        ];

        $app['matey.model_manager.factory'] = $app->share(function ($app) {
            return new ModelManagerFactory(
                $app['matey.model'],
                $app['db'],
                $app['predis']
            );
        });

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


    }

    public function boot(Application $app)
    {
        // TODO: Implement boot() method.
    }


}