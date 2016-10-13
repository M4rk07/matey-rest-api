<?php

namespace App;

use Silex\Application;

class ServicesLoader
{
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function bindServicesIntoContainer()
    {
        $this->app['notes.service'] = $this->app->share(function () {
            //return new Services\NotesService($this->app["db"]);
            return new Services\NotesService();
        });

        $this->app['userPosts.service'] = $this->app->share(function () {
            //return new Services\UserPostsService();
            return new Services\UserPostsService();
        });

        $this->app['login.service'] = $this->app->share(function () {
            return new Services\LoginService();
        });

        $this->app['registration.service'] = $this->app->share(function () {
            return new Services\RegistrationService();
        });

        $this->app['follower.service'] = $this->app->share (function() {
           return new Services\FollowerService();
        });

        $this->app['post.service'] = $this->app->share (function() {
            return new Services\PostService();
        });

        $this->app['newsfeed.service'] = $this->app->share (function() {
            return new Services\NewsFeedService();
        });
    }
}

