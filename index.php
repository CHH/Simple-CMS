<?php

define("APPROOT",      realpath(dirname(__FILE__)));

define("ENVIRONMENT",  "development");
define("LIBRARY_PATH", APPROOT . "/library");
define("PLUGINS",      APPROOT . "/plugins");

set_include_path(LIBRARY_PATH . PATH_SEPARATOR . get_include_path());

function autoloadLibraries($class)
{
  $file = str_replace(
    "_",
    DIRECTORY_SEPARATOR,
    str_replace("\\", DIRECTORY_SEPARATOR, $class)
  );

  @include_once $file . ".php";
}

spl_autoload_register("autoloadLibraries");

$bundleConfig  = parse_ini_file(APPROOT . DIRECTORY_SEPARATOR . "bundle.ini", true);

$depender = new Depender(array_keys($bundleConfig["bundle"]));

try {
  $depender->setLoadPath(get_include_path())
           ->checkAll();
  
} catch (DependencyNotInstalledException $e) {
  /*
   * If the Spark Namespace is not found, look for a checkout of Spark 
   * in the parent folder of our Installation and try one more time
   */
  if ($e->hasFailedDependency("Spark")) {
    $sparkPath = realpath(APPROOT . "/../Spark-Web-Framework/lib");
    set_include_path($sparkPath . PATH_SEPARATOR . get_include_path());
    
    try {  
      $depender->setLoadPath(get_include_path())
               ->checkAll();
    } catch (DependencyNotInstalledException $e) {
      exit($e->getMessage());
    }
  }
}

/*
 * Initialize Event Dispatcher and Front Controller
 */
$eventDispatcher = new Spark_Event_Dispatcher;
Spark_Registry::set("EventDispatcher", $eventDispatcher);

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
    "/:module/:command/:action/*", 
    array("module" => null, "command" => "default", "action" => "index")
  )
);

$pluginLoader = new PluginLoader;
$pluginLoader->setPluginPath(PLUGINS)
             ->setExport("FrontController", $frontController)
             ->setExport("EventDispatcher",          $eventDispatcher);

/*
 * This Front Controller plugin calls the beforeDispatch and afterDispatch
 * Callbacks of each Plugin
 */
$callPluginCallbacksPlugin = new Controller_Plugin_CallPluginCallbacks(
  $pluginLoader->getPluginRegistry()
);

/*
 * Connect Front Controller Events to Front Controller Plugins
 */
$frontController->addPlugin($callPluginCallbacksPlugin); 

set_exception_handler(array($frontController, "handleException"));

/*
 * Load all plugins in the plugin folder
 */
$pluginLoader->loadDirectory();

$frontController->handleRequest();

unset(
  $frontController, 
  $router,
  $callPluginCallbacksPlugin,
  $pluginLoader,
  $depender
);
