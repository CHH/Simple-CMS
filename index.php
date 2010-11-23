<?php

define("APPROOT",      realpath(dirname(__FILE__)));
define("LIBRARY_PATH", APPROOT . DIRECTORY_SEPARATOR . "library");
define("PLUGINS",      APPROOT . DIRECTORY_SEPARATOR . "plugins");

require_once "Zend/Loader.php";
require_once LIBRARY_PATH . DIRECTORY_SEPARATOR . "Autoloader.php";

// Fallback loader
$libraryLoader = new Autoloader(array("include_path" => LIBRARY_PATH));
$libraryLoader->register();

// If Spark is not in the LIBRARY_PATH, then load it from a checkout in the parent folder
if (!is_dir(LIBRARY_PATH . "/Spark")) {
    $sparkLoader = new Autoloader(array(
        "include_path" => realpath(APPROOT . "/../Spark-Web-Framework/lib/")
    ));
    $sparkLoader->register();
}

// Fallback Loader
$fallback = new Autoloader();
$fallback->register();

$config = new Zend_Config_Ini(APPROOT . "/config.ini");

if (isset($config->env)) {
    define("ENVIRONMENT",  $config->env);
} else {
    define("ENVIRONMENT", "production");
}

// Force error reporting in development environment
if (ENVIRONMENT === "development") {
    ini_set("display_errors", true);
    error_reporting(E_ALL | E_STRICT);
}

// Initialize event dispatcher and front controller
$eventDispatcher = new Spark_Event_Dispatcher;

$frontController = new Spark_Controller_FrontController;
$frontController->setEventDispatcher($eventDispatcher);
$frontController->getResolver()->setModuleDirectory(PLUGINS);

// Add our own Default Route, taking plugins and our default settings for names in accout
$router = $frontController->getRouter();
$router->removeDefaultRoutes();
$defaultRoute = new Zend_Controller_Router_Route(
	"/:module/:controller/:action/*", 
	array("module" => null, "controller" => "index", "action" => "index")
);

$router->addRoute("commands", $defaultRoute);

// Set up Plugin search path and some standard exports
$pluginLoader = new StandardPluginLoader;
$pluginLoader->setPluginPath(PLUGINS);

$exports = $pluginLoader->getExports();

$exports->set("FrontController", $frontController)
        ->set("EventDispatcher", $eventDispatcher)
        ->set("Config", $config);

/*
 * This Front Controller plugin calls the beforeDispatch and afterDispatch
 * Callbacks of each Plugin
 */
$pluginCallbacks = new Controller_Plugin_PluginCallbacks(
	array("plugins" => $pluginLoader->getPluginRegistry())
);
$frontController->addPlugin($pluginCallbacks); 

// Exceptions should be handled by the Front Controller
set_exception_handler(array($frontController, "handleException"));

// Load all plugins in the plugin folder
$pluginLoader->loadDirectory();
$frontController->handleRequest();

// Some cleanup
unset(
    $libraryLoader,
    $sparkLoader,
    $fallback,
    $defaultRoute,
    $exports,
    $config,
    $eventDispatcher,
    $frontController, 
    $router,
    $callPluginCallbacksPlugin,
    $pluginLoader
);
