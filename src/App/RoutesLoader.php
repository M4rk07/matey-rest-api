<?php

namespace App;

use App\Controllers\LoginController;
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

        $this->app['login.controller'] = $this->app->share(function () {
            return new LoginController($this->app['login.service']);
        });

        $this->app['registration.controller'] = $this->app->share(function () {
            return new RegistrationController($this->app['registration.service']);
        });
    }

    public function bindRoutesToControllers()
    {
        $api = $this->app["controllers_factory"];

        $this->app->post('/login', 'login.controller:loginAction');
        $this->app->post('/register/user', 'registration.controller:registerStandardUserAction');

        // TESTING--------------------------------------------------
        $api->get('/notes', "notes.controller:getAll");
        $api->post('/notes', "notes.controller:save");
        $api->put('/notes/{id}', "notes.controller:update");
        $api->delete('/notes/{id}', "notes.controller:delete");
        // TESTING----------------------------------------------------


        $this->app->mount($this->app["api.endpoint"].'/'.$this->app["api.version"], $api);
    }

}

