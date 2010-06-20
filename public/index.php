<?php

define("APPROOT", realpath(dirname(__FILE__) . "/../"));

define("WEBROOT", realpath(dirname(__FILE__)));

define("CONFIGS", realpath(dirname(__FILE__) . "/../config"));

define("LIBRARY_PATH", realpath(dirname(__FILE__) . "/../library"));

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

$bundleConfig  = parse_ini_file(CONFIGS . DIRECTORY_SEPARATOR . "bundle.ini", true);

$depender = new Depender(array_keys($bundleConfig["bundle"]));

try {
  $depender->setLoadPath(get_include_path())
           ->checkAll();
  
} catch (DependencyNotInstalledException $e) {
  
  try {
    /**
     * If the Spark Namespace is not found, look for a checkout of Spark 
     * in the parent folder of our Installation
     */
    if (in_array("Spark", $e->getFailedDependencies())) {
      $sparkPath = realpath(dirname(__FILE__) . "/../../Spark-Web-Framework/lib");
      set_include_path($sparkPath . PATH_SEPARATOR . get_include_path());
      
      $depender->setLoadPath(get_include_path())
               ->checkAll();
    }
  } catch (DependencyNotInstalledException $e) {
    print $e->getMessage();
    exit();
  }
}


$coreConfig  = new Zend_Config_Ini(CONFIGS . DIRECTORY_SEPARATOR . "core.ini");
$pagesConfig = new Zend_Config_Ini(CONFIGS . DIRECTORY_SEPARATOR . "pages.ini");

if (!defined("APPLICATION_ENVIRONMENT")) {
  $environment = "production";
  
  if (isset($coreConfig->application->environment)) {
    $environment = $coreConfig->application->environment;
  }
  
  define("APPLICATION_ENVIRONMENT", $environment);
}

define("PLUGINS", $coreConfig->Spark_Controller_CommandResolver->module_directory);

Spark_Object_Manager::setConfig($coreConfig);

$eventDispatcher = new Spark_Event_Dispatcher;
$frontController = Spark_Object_Manager::create("Spark_Controller_FrontController");

$frontController->setEventDispatcher($eventDispatcher);

Spark_Registry::set("CoreConfig", $coreConfig);
Spark_Registry::set("PagesConfig", $pagesConfig);
Spark_Registry::set("EventDispatcher", $eventDispatcher);

$router = $frontController->getRouter();

$router->removeDefaultRoutes();

$router->addRoute(
  "commands", 
  new Zend_Controller_Router_Route(
    "/:module/:command/:action/*", 
    array("module" => null, "command" => "default", "action" => "default")
  )
);

$layoutPlugin = new Spark_Controller_Plugin_Layout($pagesConfig->pages->layout);

$layoutPlugin->getLayout()->addHelperPath(
  "Spark" . DIRECTORY_SEPARATOR . "View" . DIRECTORY_SEPARATOR . "Helper", 
  "Spark_View_Helper"
);

$pluginLoader = new PluginLoader;

$pluginLoader->setPluginPath(PLUGINS)
             ->setPluginOption("frontController", $frontController)
             ->setPluginOption("layoutPlugin", $layoutPlugin);

/**
 * This Front Controller plugin calls the beforeDispatch and afterDispatch
 * Callbacks of each Plugin
 */
$callPluginCallbacksPlugin = new Controller_Plugin_CallPluginCallbacks(
  $pluginLoader->getPluginRegistry()
);

/**
 * Connect Front Controller Events to Front Controller Plugins
 */
$eventDispatcher
  ->on(
    Spark_Controller_FrontController::EVENT_AFTER_DISPATCH, 
    $layoutPlugin
  )
  ->on(
    Spark_Controller_FrontController::EVENT_AFTER_DISPATCH, 
    $callPluginCallbacksPlugin
  )
  ->on(
    Spark_Controller_FrontController::EVENT_BEFORE_DISPATCH,
    $callPluginCallbacksPlugin
  );

set_exception_handler(array($frontController, "handleException"));

/**
 * Load all plugins in the plugin folder
 */
$pluginLoader->loadDirectory();

$frontController->handleRequest();

unset(
  $frontController, 
  $router, 
  $layoutPlugin, 
  $callPluginCallbacksPlugin,
  $pluginLoader,
  $depender
);
