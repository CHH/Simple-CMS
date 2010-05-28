<?php

define("APPROOT", realpath(dirname(__FILE__) . "/../"));

define("WEBROOT", realpath(dirname(__FILE__)));

define("CONFIGS", realpath(dirname(__FILE__) . "/../config"));

define("LIBRARY_PATH", realpath(dirname(__FILE__) . "/../library"));

if(!defined("APPLICATION_ENVIRONMENT")) {
  define("APPLICATION_ENVIRONMENT", "production");
}

set_include_path(LIBRARY_PATH . PATH_SEPARATOR . get_include_path());

/**
 * Check if all dependencies specified in the Core Config are installed
 */
require_once("Depender.php");
require_once("Bundler.php");
require_once("DependencyNotInstalledException.php");

$bundleConfig  = parse_ini_file(CONFIGS . DIRECTORY_SEPARATOR . "bundle.ini", true);

$depender = new Depender(array_keys($bundleConfig["bundle"]));
$bundler  = new Bundler;

try {
  $depender->setLoadPath(get_include_path())
           ->checkAll();
  
} catch (DependencyNotInstalledException $e) {
  
  try {
    /**
     * If Spark is not found, look for a checkout of Spark 
     * in the parent folder of our Installation
     */
    if (in_array("Spark", $e->getFailedDependencies())) {
      $sparkPath = realpath(dirname(__FILE__) . "/../../Spark-Web-Framework/lib");
      
      set_include_path($sparkPath . PATH_SEPARATOR . get_include_path());
      
      $depender->setLoadPath(get_include_path())
               ->checkAll();
    }
    
  } catch (DependencyNotInstalledException $e) {
    /**
     * Attempt to bundle the failed Dependencies
     */
    $bundler->setLoadPath(LIBRARY_PATH);
    
    foreach($e->getFailedDependencies() as $dependency) {
      $bundler->bundle($dependency, $bundleConfig["bundle"][$dependency]);
    }
    
    exit(); 
  }
}

require_once("Zend/Config/Ini.php");

$coreConfig  = new Zend_Config_Ini(CONFIGS . DIRECTORY_SEPARATOR . "core.ini");
$pagesConfig = new Zend_Config_Ini(CONFIGS . DIRECTORY_SEPARATOR . "pages.ini");

define("PLUGINS", $coreConfig->Spark_Controller_CommandResolver->module_directory);

function autoloadLibraries($class)
{
  @include_once str_replace("_", DIRECTORY_SEPARATOR, $class) . ".php";
}

spl_autoload_register("autoloadLibraries");

/**
 * Write the Core Config to the Registry
 */
Spark_Registry::set("CoreConfig", $coreConfig);
Spark_Registry::set("PagesConfig", $pagesConfig);

Spark_Object_Manager::setConfig($coreConfig);

$frontController = Spark_Object_Manager::create("Spark_Controller_FrontController");

$router = $frontController->getRouter();

$router->removeDefaultRoutes();

$router->addRoute("commands", new Zend_Controller_Router_Route("/:module/:command/:action/*", array("module"=>null, "command"=>"default", "action" => "default")));

$layoutPlugin = Spark_Object_Manager::get("Spark_Controller_Plugin_Layout", $pagesConfig->pages->layout);
$layoutPlugin->getLayout()->addHelperPath("Spark" . DIRECTORY_SEPARATOR . "View" . DIRECTORY_SEPARATOR . "Helper", "Spark_View_Helper");

// Load the plugins
$pluginLoader = new PluginLoader;

$pluginLoader->setPluginPath(PLUGINS)
             ->setPluginOption("frontController", $frontController)
             ->setPluginOption("layout", $layoutPlugin)
             ->loadDirectory();

$callPluginCallbacksPlugin = new Controller_Plugin_CallPluginCallbacks($pluginLoader->getPluginRegistry());

Spark_Event_Dispatcher::getInstance()
  ->on(Spark_Controller_FrontController::EVENT_AFTER_DISPATCH, $callPluginCallbacksPlugin)
  ->on(Spark_Controller_FrontController::EVENT_BEFORE_DISPATCH, $callPluginCallbacksPlugin)
  ->on(Spark_Controller_FrontController::EVENT_AFTER_DISPATCH, $layoutPlugin);
  
set_exception_handler(array($frontController, "handleException"));

$frontController->handleRequest();

unset($frontController, $router, $layoutPlugin);
