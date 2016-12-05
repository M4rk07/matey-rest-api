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
    }

    public function bindRoutesToControllers()
    {
        $api = $this->app["controllers_factory"];

        // OAuth 2.0 controllers
        $this->app->get('/api/oauth2/authorize', 'authbucket_oauth2.oauth2_controller:authorizeAction')
            ->bind('api_oauth2_authorize');

        $this->app->post('/api/oauth2/token', 'authbucket_oauth2.oauth2_controller:tokenAction')
            ->bind('api_oauth2_token');

        $this->app->match('/api/oauth2/debug', 'authbucket_oauth2.oauth2_controller:debugAction')
            ->bind('api_oauth2_debug');
        // -------------------------------------------------------------------------------------

        // Matey API controllers
        $this->app->post('/devices', 'matey.device_controller:createDeviceAction');
        $this->app->put('/devices/{deviceId}', 'matey.device_controller:updateDeviceAction');

        $this->app->post('/users/accounts', 'matey.account_controller:createAccountAction');
        $api->post('/users/me/accounts', 'matey.account_controller:createNewAccountAction');

        $api->put('/users/me/devices/{deviceId}/login', 'matey.device_controller:loginOnDeviceAction');
        $api->delete('/users/me/devices/{deviceId}/login', 'matey.device_controller:logoutOfDeviceAction');

        $api->get('/users/{userId}', 'matey.user_controller:getUserAction');
        $api->get('/users/{userId}/profile', 'matey.user_controller:getUserAction');
        $api->post('/users/me/users/{id}/follow', 'matey.user_controller:followAction'); // deprecated
        $api->delete('/users/me/users/{id}/follow', 'matey.user_controller:followAction'); // deprecated
        $api->post('/users/me/following/{id}', 'matey.user_controller:followAction');
        $api->delete('/users/me/following/{id}', 'matey.user_controller:followAction');
        $api->get('/users/{userId}/followers', 'matey.user_controller:getFollowersAction');
        $api->get('/users/{userId}/following', 'matey.user_controller:getFollowingAction');

        $this->app->mount($this->app["api.endpoint"].'/'.$this->app["api.version"], $api);
    }

}

