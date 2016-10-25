<?php

namespace App;

use App\Controllers\FollowerController;
use App\Controllers\InterestController;
use App\Controllers\LoginController;
use App\Controllers\NewsFeedController;
use App\Controllers\PostController;
use App\Controllers\RegistrationController;
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
            return new RegistrationController($this->app['registration.service'], $this->app['redis.service'], $this->app['validator']);
        });

        $this->app['login.controller'] = $this->app->share(function () {
            return new LoginController($this->app['login.service'], $this->app['redis.service'], $this->app['validator']);
        });

        $this->app['follower.controller'] = $this->app->share(function () {
            return new FollowerController($this->app['follower.service'], $this->app['redis.service'], $this->app['validator']);
        });

        $this->app['post.controller'] = $this->app->share(function () {
            return new PostController($this->app['post.service'], $this->app['redis.service'], $this->app['validator']);
        });

        $this->app['newsfeed.controller'] = $this->app->share(function () {
            return new NewsFeedController($this->app['newsfeed.service'], $this->app['redis.service'], $this->app['validator']);
        });

        $this->app['interest.controller'] = $this->app->share(function () {
            return new InterestController($this->app['interest.service'], $this->app['redis.service'], $this->app['validator']);
        });
    }

    public function bindRoutesToControllers()
    {
        $api = $this->app["controllers_factory"];

        $this->app->post('/register/user', 'registration.controller:registerStandardUserAction');
        $this->app->post('/register/device', 'registration.controller:registerDeviceAction');
        $this->app->post('/authenticate/social', 'registration.controller:authenticateSocialUserAction');

        // API ROUTES
        $api->post('/login', 'login.controller:loginAction');
        $api->post('/logout', 'login.controller:logoutAction');

        // for "action" valid values are:
        // - follow
        // - unfollow
        $api->post('/follower/{action}', 'follower.controller:followerAction');

        $api->post('/post/add', 'post.controller:addPostAction');
        $api->post('/post/remove', 'post.controller:deletePostAction');
        $api->post('/post/response/add', 'post.controller:addResponseAction');
        $api->post('/post/response/remove', 'post.controller:deleteResponseAction');
        $api->post('/post/response/approve', 'post.controller:approveAction');
        $api->post('/subinterest/add', 'interest.controller:addSubinterestAction');

        $api->get('/newsfeed', 'newsfeed.controller:getNewsFeedAction');

        $this->app->mount($this->app["api.endpoint"].'/'.$this->app["api.version"], $api);
    }

}

