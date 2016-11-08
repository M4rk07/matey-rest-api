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

        $this->app->post('/account/create/{accountType}', 'matey.account_controller:createAccountAction');
        $api->post('/account/merge/{accountType}', 'matey.account_controller:mergeAccountAction');

        $this->app->post('/register/{action}', 'matey.registration_controller:registerUserAction');

        //$this->app->post('/register/user', 'registration.controller:registerStandardUserAction');
        //$this->app->post('/register/device', 'registration.controller:registerDeviceAction');
        $this->app->post('/authenticate/social', 'registration.controller:authenticateSocialUserAction');

        // for "action" valid values are:
        // - facebook
        // - standard
        $api->post('/merge/{action}', 'registration.controller:mergeAccountsAction');

        // API ROUTES
        $api->post('/login', 'login.controller:loginAction');
        $api->post('/login/merge/{action}', 'registration.controller:mergeAccountsAction');
        $api->post('/logout', 'login.controller:logoutAction');

        // for "action" valid values are:
        // - follow
        // - unfollow
        $api->post('/follower/{action}', 'follower.controller:followerAction');

        $api->post('/post/add', 'post.controller:addPostAction');
        $api->post('/post/remove', 'post.controller:deletePostAction');
        $api->post('/response/add', 'response.controller:addResponseAction');
        $api->post('/response/remove', 'response.controller:deleteResponseAction');
        $api->post('/response/approve', 'response.controller:approveAction');

        $api->get('/profile_picture/{user_id}', 'user_profile.controller:getProfilePictureAction');

        // -------------------------------------------------------------------------------------


        $this->app->get('/test', 'test.controller:fillCategories');
        $this->app->get('/fillGroups', 'test.controller:fillGroups');
        $this->app->get('/signature', 'registration.controller:makeSignature');

        $api->post('/interests/add', 'interest.controller:addInterestsAction');
        $api->get('/interests', 'interest.controller:showInterestsAction');

        $api->get('/newsfeed', 'newsfeed.controller:getNewsFeedAction');

        $api->get('/search/name', 'search.controller:searchUserByNameAction');

        $this->app->mount($this->app["api.endpoint"].'/'.$this->app["api.version"], $api);
    }

}

