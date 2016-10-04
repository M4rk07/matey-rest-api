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

        $this->app['tester.service'] = $this->app->share(function () {
            //return new Services\UserPostsService();
            return new Services\DatabaseTesterService();
        });

        $this->app['login.service'] = $this->app->share(function () {
            //return new Services\UserPostsService();
            return new Services\LoginService();
        });
    }
}

