<?php
/** @namespace */
namespace Core;

use \Spark\App;
use \Spark\Controller\HttpRequest;
use \Spark\Controller\HttpResponse;

const ENV_DEVELOPMENT = "development";
const ENV_PRODUCTION  = "production";

const APPROOT   = __DIR__;

define("LIBRARIES", APPROOT . DIRECTORY_SEPARATOR . "library");
define("PLUGINS",   APPROOT . DIRECTORY_SEPARATOR . "plugins");

require_once(LIBRARIES . '/Spark2/lib/Spark.php');
require_once(LIBRARIES . '/Core.php');

const ENVIRONMENT = ENV_DEVELOPMENT;

// Force error reporting in development environment
if (ENVIRONMENT === "development") {
    ini_set("display_errors", true);
    error_reporting(E_ALL | E_STRICT);
}

$request  = new HttpRequest;
$response = new HttpResponse;

$app = new App;

$pluginLoader = new Plugin\StandardLoader;
//$pluginLoader->loadDirectory(PLUGINS);

//$app($request, $response);
