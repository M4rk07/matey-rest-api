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

require_once __DIR__.'/../resources/config/security.php';

$app['matey.timezone'] = 'Europe/Belgrade';

date_default_timezone_set($app['matey.timezone']);

define("ROOT_PATH", __DIR__ . "/..");

# Register MUST have Silex providers for AuthBucketOAuth2ServiceProvider.
$app->register(new MonologServiceProvider(), array(
    "monolog.logfile" => ROOT_PATH . "/storage/logs/" . Carbon::now($app['matey.timezone'])->format("Y-m-d") . ".log",
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

$app->register(new \App\Provider\MateyServiceProvider());

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

$app->error(function (\Exception $e, $code) use ($app) {
    $app['monolog']->addError($e->getMessage());
    $app['monolog']->addError($e->getTraceAsString());
    return new JsonResponse(array("statusCode" => $code, "message" => $e->getMessage(), "stacktrace" => $e->getTraceAsString()));
});

$app['security.encoder.digest'] = $app->share(function ($app) {
    return new Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder();
});

//load services
$servicesLoader = new ServicesLoader($app);
$servicesLoader->bindServicesIntoContainer();

//load routes
$routesLoader = new RoutesLoader($app);
$routesLoader->bindRoutesToControllers();

return $app;
