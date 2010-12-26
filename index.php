<?php
/** @namespace */
namespace Core;

use \Spark\App,
    \Spark\HttpRequest,
    \Spark\HttpResponse,
    \Spark\Controller;

const ENV_DEVELOPMENT = "development";
const ENV_PRODUCTION  = "production";

const APPROOT = __DIR__;

define("LIBRARIES", APPROOT . DIRECTORY_SEPARATOR . "library");
define("PLUGINS",   APPROOT . DIRECTORY_SEPARATOR . "plugins");

// Look for a checkout of Spark in the parent folder, otherwise take the bundled version
if (file_exists("../Spark2/lib/Spark.php")) {
    require_once('../Spark2/lib/Spark.php');
} else {
    require_once(LIBRARIES . '/Spark2/lib/Spark.php');
}

require_once(LIBRARIES . '/Mustache/library/Phly/Mustache/_autoload.php');
require_once(LIBRARIES . '/Textile.php');
require_once(LIBRARIES . '/Core.php');

$config = parse_ini_file('config.ini', true);
$env    = isset($config["env"]) ? $config["env"] : ENV_PRODUCTION;

define("ENVIRONMENT", $env);

// Force error reporting in development environment
if (ENVIRONMENT === "development") {
    ini_set("display_errors", true);
    error_reporting(E_ALL | E_STRICT);
}

$request  = new HttpRequest;
$response = new HttpResponse;

$app = new App;
$mvc = new Controller\CallbackFilter;

$resolver = $mvc->getResolver();
$resolver->setNamingSpec("\\Plugin\\{{module}}\\Controller\\{{controller}}Controller");

$app->preDispatch($mvc);

$pluginEnv = new Plugin\Environment;

$pluginEnv
    ->export("App",    $app)
    ->export("Routes", $app->routes)
    ->export("Config", $config);

$pluginLoader = new Plugin\StandardLoader(array(
    "path" => PLUGINS,
    "environment" => $pluginEnv
));

Plugin\Controller::setEnvironment($pluginEnv);
$pluginLoader->loadAll();

$app($request, $response);
