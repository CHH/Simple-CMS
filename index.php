<?php

define("APPROOT",       realpath(dirname(__FILE__)));
define("LIBRARY_PATH", APPROOT . DIRECTORY_SEPARATOR . "library");
define("PLUGINS",       APPROOT . DIRECTORY_SEPARATOR . "plugins");

set_include_path(LIBRARY_PATH . PATH_SEPARATOR . get_include_path());

require_once "Autoloader.php";

$autoloader = new Autoloader();
$autoloader->register();

// Look for Spark in the parent folder
if (!is_dir(LIBRARY_PATH . "/Spark")) {
    $sparkPath = realpath(APPROOT . "/../Spark-Web-Framework/lib");
    set_include_path($sparkPath . PATH_SEPARATOR . get_include_path());
}

$config = new Zend_Config_Ini(APPROOT . "/config.ini");
Spark_StaticRegistry::set("config", $config);

if (isset($config->env)) {
    define("ENVIRONMENT",  $config->env);
} else {
    define("ENVIRONMENT", "production");
}

/*
 * Force Error Reporting in Development Environment if host has it disabled
 */
if (ENVIRONMENT === "development") {
    ini_set("display_errors", true);
    error_reporting(E_ALL | E_STRICT);
}

/*
 * Initialize Event Dispatcher and Front Controller
 */
$eventDispatcher = new Spark_Event_Dispatcher;
Spark_StaticRegistry::set("EventDispatcher", $eventDispatcher);

$frontController = new Spark_Controller_FrontController;
$frontController->setEventDispatcher($eventDispatcher);
$frontController->getResolver()->setModuleDirectory(PLUGINS);

/*
 * Add our own Default Route, taking plugins and our default settings for names in accout
 */
$router = $frontController->getRouter();
$router->removeDefaultRoutes();
$router->addRoute(
  "commands", 
  new Zend_Controller_Router_Route(
    "/:module/:controller/:action/*", 
    array("module" => null, "controller" => "index", "action" => "index")
  )
);

/*
 * Set up Plugin search path and some standard exports
 */
$pluginLoader = new StandardPluginLoader;
$pluginLoader->setPluginPath(PLUGINS)
             ->setExport("FrontController", $frontController)
             ->setExport("EventDispatcher", $eventDispatcher);

/*
 * This Front Controller plugin calls the beforeDispatch and afterDispatch
 * Callbacks of each Plugin
 */
$pluginCallbacks = new Controller_Plugin_PluginCallbacks(
  array("plugins" => $pluginLoader->getPluginRegistry())
);
$frontController->addPlugin($pluginCallbacks); 

/*
 * Exceptions should be handled by the Front Controller
 */
set_exception_handler(array($frontController, "handleException"));

/*
 * Load all plugins in the plugin folder
 */
$pluginLoader->loadDirectory();

$frontController->handleRequest();

unset(
  $config,
  $frontController, 
  $router,
  $callPluginCallbacksPlugin,
  $pluginLoader
);
