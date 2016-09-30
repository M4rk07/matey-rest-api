<?php

namespace App;

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
        $this->app['notes.controller'] = $this->app->share(function () {
            return new Controllers\NotesController($this->app['notes.service']);
        });

        $this->app['userPosts.controller'] = $this->app->share(function () {
            return new Controllers\UserPostsController($this->app['userPosts.service']);
        });

        $this->app['tester.controller'] = $this->app->share(function () {
            return new Controllers\DatabaseTesterController($this->app['tester.service']);
        });
    }

    public function bindRoutesToControllers()
    {
        $api = $this->app["controllers_factory"];

        $api->get('/notes', "notes.controller:getAll");
        $api->post('/notes', "notes.controller:save");
        $api->put('/notes/{id}', "notes.controller:update");
        $api->delete('/notes/{id}', "notes.controller:delete");

        $api->get('/posts/news_feed/{id_user_requesting}', "userPosts.controller:fetchNewsFeedPosts");

        // TESTING
        $api->get('/tester/fillUsersTable', "tester.controller:fillUsersTable");
        $api->get('/tester/deleteAllUsersTable', "tester.controller:deleteAllUsersTable");

        $this->app->mount($this->app["api.endpoint"].'/'.$this->app["api.version"], $api);
    }

}

