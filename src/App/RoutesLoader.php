<?php

namespace App;

use App\Controllers\FollowerController;
use App\Controllers\InterestController;
use App\Controllers\LoginController;
use App\Controllers\NewsFeedController;
use App\Controllers\PostController;
use App\Controllers\RegistrationController;
use App\Controllers\ResponseController;
use App\Controllers\SearchController;
use App\Controllers\TestController;
use App\Controllers\UserProfileController;
use App\Services\BaseService;
use App\Services\Redis\UserRedisService;
use App\Services\Redis\UserService;
use Silex\Application;

class RoutesLoader
{
    private $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->instantiateControllers();

    }

    private function instantiateControllers()
    {
        $this->app['registration.controller'] = $this->app->share(function () {
            return new RegistrationController($this->app['matey.service'], $this->app['redis.service'], $this->app['validator']);
        });

        $this->app['login.controller'] = $this->app->share(function () {
            return new LoginController($this->app['matey.service'], $this->app['redis.service'], $this->app['validator']);
        });

        $this->app['follower.controller'] = $this->app->share(function () {
            return new FollowerController($this->app['matey.service'], $this->app['redis.service'], $this->app['validator']);
        });

        $this->app['post.controller'] = $this->app->share(function () {
            return new PostController($this->app['matey.service'], $this->app['redis.service'], $this->app['validator']);
        });

        $this->app['response.controller'] = $this->app->share(function () {
            return new ResponseController($this->app['matey.service'], $this->app['redis.service'], $this->app['validator']);
        });

        $this->app['newsfeed.controller'] = $this->app->share(function () {
            return new NewsFeedController($this->app['newsfeed.service'], $this->app['redis.service'], $this->app['validator']);
        });

        $this->app['interest.controller'] = $this->app->share(function () {
            return new InterestController($this->app['interest.service'], $this->app['redis.service'], $this->app['validator']);
        });

        $this->app['search.controller'] = $this->app->share(function () {
            return new SearchController($this->app['search.service'], $this->app['redis.service'], $this->app['validator']);
        });

        $this->app['user_profile.controller'] = $this->app->share(function () {
            return new UserProfileController(new BaseService(), $this->app['redis.service'], $this->app['validator']);
        });

        $this->app['test.controller'] = $this->app->share(function () {
            return new TestController($this->app['test.service'], $this->app['redis.service'], $this->app['validator']);
        });
    }

    public function bindRoutesToControllers()
    {
        $api = $this->app["controllers_factory"];

        // OAuth 2.0 ROUTES
        $this->app->get('/api/oauth2/authorize', 'authbucket_oauth2.oauth2_controller:authorizeAction')
            ->bind('api_oauth2_authorize');

        $this->app->post('/api/oauth2/token', 'authbucket_oauth2.oauth2_controller:tokenAction')
            ->bind('api_oauth2_token');

        $this->app->match('/api/oauth2/debug', 'authbucket_oauth2.oauth2_controller:debugAction')
            ->bind('api_oauth2_debug');
        // -------------------------------------------------------------------------------------


        $this->app->post('/devices', 'matey.device_controller:createDeviceAction');
        $this->app->put('/devices/{deviceId}', 'matey.device_controller:updateDeviceAction');

        $this->app->post('/users/accounts', 'matey.account_controller:createAccountAction');
        $api->post('/users/me/accounts', 'matey.account_controller:mergeAccountAction');
        $api->put('/users/me/devices/{deviceId}/login', 'matey.device_controller:loginOnDeviceAction');
        $api->get('/users/{userId}/profile', 'matey.user_controller:getUserAction');
        $api->post('/users/me/users/{id}/{action}', 'matey.user_controller:followAction');

        $this->app->mount($this->app["api.endpoint"].'/'.$this->app["api.version"], $api);
    }

}

