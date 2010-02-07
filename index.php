<?php

define("WEBROOT", realpath(dirname(__FILE__)));

define("CONFIGS", realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . "config"));

define("APPLICATION_PATH", realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR 
       . "application"));

define("LIBRARY_PATH", realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . "library"));

$coreConfig = new Zend_Config_Ini(CONFIGS . DIRECTORY_SEPARATOR . "core.ini");
$pagesConfig = new Zend_Config_Ini(CONFIGS . DIRECTORY_SEPARATOR . "pages.ini");

if(isset($coreConfig->framework->path) and is_dir($coreConfig->framework->path)) {
  define("SPARK_PATH", $coreConfig->framework->path);
} else {
  // Assume a cloned Spark Repo in the parent folder
  $sparkPath = realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . ".." .
    DIRECTORY_SEPARATOR . "Spark-Web-Framework" . DIRECTORY_SEPARATOR . "lib");
  
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


set_include_path(LIBRARY_PATH . PATH_SEPARATOR . SPARK_PATH . PATH_SEPARATOR . get_include_path());

function __autoload($class)
{
  include_once str_replace("_", DIRECTORY_SEPARATOR, $class) . ".php";
}

// Write the Core Config to the Registry
Spark_Registry::set("CoreConfig", $coreConfig);
Spark_Registry::set("PagesConfig", $pagesConfig);

Spark_Object_Manager::setConfig($coreConfig);

$frontController = Spark_Object_Manager::get("Spark_Controller_FrontController");

$router = $frontController->getRouter();

$router->removeDefaultRoutes();

$router->addRoute("commands", new Zend_Controller_Router_Route(":command/:action/*", array("command"=>"default", "action" => "default")));
$router->addRoute("pages", new Zend_Controller_Router_Route(":name", array("command"=>"page", "name" => "index")));


$applyLayoutFilter = Spark_Object_Manager::create("Spark_Controller_Filter_ApplyLayout", $pagesConfig->pages);

$frontController->addPostFilter($applyLayoutFilter);

$frontController->handleRequest();
