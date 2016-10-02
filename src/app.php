<?php

use Silex\Application;
use Silex\Provider\HttpCacheServiceProvider;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\MonologServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\ServicesLoader;
use App\RoutesLoader;
use Carbon\Carbon;

//date_default_timezone_set('Europe/London');

define("ROOT_PATH", __DIR__ . "/..");

//handling ERRORS
$app->error(function (\Exception $e, Request $request, $code) {

    return $e->getMessage();

});

//handling CORS preflight request
$app->before(function (Request $request) {
   if ($request->getMethod() === "OPTIONS") {
       $response = new Response();
       $response->headers->set("Access-Control-Allow-Origin","*");
       $response->headers->set("Access-Control-Allow-Methods","GET,POST,PUT,DELETE,OPTIONS");
       $response->headers->set("Access-Control-Allow-Headers","Content-Type");
       $response->setStatusCode(200);
       return $response->send();
   }
}, Application::EARLY_EVENT);

//handling CORS respons with right headers
$app->after(function (Request $request, Response $response) {
   $response->headers->set("Access-Control-Allow-Origin","*");
   $response->headers->set("Access-Control-Allow-Methods","GET,POST,PUT,DELETE,OPTIONS");
});

//accepting JSON
$app->before(function (Request $request) {
    if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
        $data = json_decode($request->getContent(), true);
        $request->request->replace(is_array($data) ? $data : array());
    }
});

# Register MUST have Silex providers for AuthBucketOAuth2ServiceProvider.
$app->register(new MonologServiceProvider(), array(
    "monolog.logfile" => ROOT_PATH . "/storage/logs/" . Carbon::now('Europe/London')->format("Y-m-d") . ".log",
    "monolog.level" => $app["log.level"],
    "monolog.name" => "application"
));
$app->register(new Silex\Provider\SecurityServiceProvider());
$app->register(new Silex\Provider\ValidatorServiceProvider());

# Register AuthBucketOAuth2ServiceProvider.
$app->register(new AuthBucket\OAuth2\Provider\AuthBucketOAuth2ServiceProvider());

$app->register(new ServiceControllerServiceProvider());

$app->register(new DoctrineServiceProvider(), array(
	"db.options" => array(
        "driver" => "pdo_mysql",
        "dbname" => "matey_db_v1",
        "host" => "localhost",
        "user" => "root",
        "password" => "maka",
        "charset" => "utf8mb4"
    ),
));

$app->register(new HttpCacheServiceProvider(), array("http_cache.cache_dir" => ROOT_PATH . "/storage/cache",));

$app['security.encoder.digest'] = $app->share(function ($app) {
    return new Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder();
});


/*$app['security.user_provider.default'] = $app['security.user_provider.inmemory._proto']([
    'root' => ['ROLE_USER', 'root'],
    'demousername2' => ['ROLE_USER', 'demopassword2'],
    'demousername3' => ['ROLE_USER', 'demopassword3'],
]);*/

$app['security.firewalls'] = [
    'api_oauth2_authorize' => [
        'pattern' => '^/api/oauth2/authorize$',
        'http' => true,
        //'users' => $app['security.user_provider.default']
        'users' => $app['authbucket_oauth2.user_provider']
    ],
    'api_oauth2_token' => [
        'pattern' => '^/api/oauth2/token$',
        'oauth2_token' => true,
    ],
    //'api_resource' => [
    //    'pattern' => '^/api/v1',
    //    'oauth2_resource' => true,
    //]
];

//load services
$servicesLoader = new App\ServicesLoader($app);
$servicesLoader->bindServicesIntoContainer();

//load routes
$routesLoader = new App\RoutesLoader($app);
$routesLoader->bindRoutesToControllers();

// OAuth 2.0 ROUTES
$app->get('/api/oauth2/authorize', 'authbucket_oauth2.oauth2_controller:authorizeAction')
    ->bind('api_oauth2_authorize');

$app->post('/api/oauth2/token', 'authbucket_oauth2.oauth2_controller:tokenAction')
    ->bind('api_oauth2_token');

$app->match('/api/oauth2/debug', 'authbucket_oauth2.oauth2_controller:debugAction')
    ->bind('api_oauth2_debug');

$app->error(function (\Exception $e, $code) use ($app) {
    $app['monolog']->addError($e->getMessage());
    $app['monolog']->addError($e->getTraceAsString());
    return new JsonResponse(array("statusCode" => $code, "message" => $e->getMessage(), "stacktrace" => $e->getTraceAsString()));
});

return $app;
