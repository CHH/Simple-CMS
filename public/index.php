<?php

define("APPROOT", realpath(dirname(__FILE__) . "/../"));

define("WEBROOT", realpath(dirname(__FILE__)));

define("CONFIGS", realpath(dirname(__FILE__) . "/../config"));

define("LIBRARY_PATH", realpath(dirname(__FILE__) . "/../library"));

if(!defined("APPLICATION_ENVIRONMENT")) {
  define("APPLICATION_ENVIRONMENT", "production");
}

require_once("Zend/Config/Ini.php");

$coreConfig = new Zend_Config_Ini(CONFIGS . DIRECTORY_SEPARATOR . "core.ini");
$pagesConfig = new Zend_Config_Ini(CONFIGS . DIRECTORY_SEPARATOR . "pages.ini");

if(isset($coreConfig->framework->path) and is_dir($coreConfig->framework->path)) {
  define("SPARK_PATH", $coreConfig->framework->path);
} else {
  // Assume a cloned Spark Repo in the parent folder of the Simple CMS install
  $sparkPath = realpath(dirname(__FILE__) . "/../../Spark-Web-Framework/lib");
  
  if(is_dir($sparkPath)) {
    define("SPARK_PATH", $sparkPath);
  } else {
    throw new Exception("Simple-CMS depends on the Spark-Web-Framework. 
     Make sure who have either cloned the Git Repository 
     (git clone git://github.com/yuri41/Spark-Web-Framework.git) 
     in the parent folder of your Simple-CMS installation 
     or you have configured the framework.path directive 
     in the core.ini file correctly.");
  }
}

define("PLUGINS", $coreConfig->Spark_Controller_CommandResolver->module_directory);

set_include_path(LIBRARY_PATH . PATH_SEPARATOR . SPARK_PATH . PATH_SEPARATOR . get_include_path());

function autoloadLibraries($class)
{
  @include_once str_replace("_", DIRECTORY_SEPARATOR, $class) . ".php";
}

spl_autoload_register("autoloadLibraries");

// Write the Core Config to the Registry
Spark_Registry::set("CoreConfig", $coreConfig);
Spark_Registry::set("PagesConfig", $pagesConfig);

Spark_Object_Manager::setConfig($coreConfig);

$frontController = Spark_Object_Manager::create("Spark_Controller_FrontController");

$router = $frontController->getRouter();

$router->removeDefaultRoutes();

$router->addRoute("commands", new Zend_Controller_Router_Route("/:module/:command/:action/*", array("module"=>null, "command"=>"default", "action" => "default")));

$layoutPlugin = Spark_Object_Manager::get("Spark_Controller_Plugin_Layout", $pagesConfig->pages->layout);
$layoutPlugin->getLayout()->addHelperPath(SPARK_PATH . DIRECTORY_SEPARATOR . "Spark" . DIRECTORY_SEPARATOR . "View" . DIRECTORY_SEPARATOR . "Helper", "Spark_View_Helper");

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
