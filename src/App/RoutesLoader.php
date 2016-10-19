<?php

namespace App;

use App\Controllers\FollowerController;
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
        // TESTING--------------------------------------------------
        $this->app['notes.controller'] = $this->app->share(function () {
            return new Controllers\NotesController($this->app['notes.service']);
        });
        // TESTING----------------------------------------------------

        $this->app['registration.controller'] = $this->app->share(function () {
            return new RegistrationController($this->app['registration.service'], $this->app['validator']);
        });

        $this->app['login.controller'] = $this->app->share(function () {
            return new LoginController($this->app['login.service'], $this->app['validator']);
        });

        $this->app['follower.controller'] = $this->app->share(function () {
            return new FollowerController($this->app['follower.service'], $this->app['validator']);
        });

        $this->app['post.controller'] = $this->app->share(function () {
            return new PostController($this->app['post.service'], $this->app['validator']);
        });

        $this->app['newsfeed.controller'] = $this->app->share(function () {
            return new NewsFeedController($this->app['newsfeed.service'], $this->app['validator']);
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

        $api->post('/follower/add', 'follower.controller:followAction');
        $api->post('/follower/remove', 'follower.controller:unfollowAction');

        $api->post('/post/add', 'post.controller:addPostAction');
        $api->post('/post/remove', 'post.controller:deletePostAction');
        $api->post('/post/response/add', 'post.controller:addResponseAction');
        $api->post('/post/response/remove', 'post.controller:deleteResponseAction');
        $api->post('/post/response/approve', 'post.controller:approveAction');

        $api->get('/newsfeed/{user_id}', 'newsfeed.controller:getNewsFeedAction');

        // TESTING--------------------------------------------------
        $api->get('/notes', "notes.controller:getAll");
        $api->post('/notes', "notes.controller:save");
        $api->put('/notes/{id}', "notes.controller:update");
        $api->delete('/notes/{id}', "notes.controller:delete");
        // TESTING----------------------------------------------------


        $this->app->mount($this->app["api.endpoint"].'/'.$this->app["api.version"], $api);
    }

}

