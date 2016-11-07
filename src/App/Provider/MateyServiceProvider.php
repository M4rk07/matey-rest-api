<?php

namespace App\Provider;
use App\Controllers\RegistrationController;
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
            'oauth2User' => 'App\\MateyModels\\OAuth2User'
        ];

        $app['matey.model_manager.factory'] = $app->share(function ($app) {
            return new ModelManagerFactory($app['matey.model'], $app['matey.db.connection']);
        });

        $app['matey.handlers.registration'] = [
            'standard' => 'App\\Handlers\\Registration\\StandardRegistrationHandler',
            'facebook' => 'App\\Handlers\\Registration\\FacebookRegistrationHandler'
        ];

        $app['matey.registration_handler.factory'] = $app->share(function($app) {
           return new RegistrationHandlerFactory(
                $app['validator'],
                $app['matey.model_manager.factory'],
                $app['matey.db.connection'],
                $app['matey.handlers.registration']
           );
        });

        $app['matey.registration_controller'] = $app->share(function () use ($app) {
            return new RegistrationController(
                $app['validator'],
                $app['matey.model_manager.factory'],
                $app['matey.registration_handler.factory']
            );
        });

    }

    public function boot(Application $app)
    {
        // TODO: Implement boot() method.
    }


}