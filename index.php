<?php

define("WEBROOT", realpath(dirname(__FILE__)));

define("APPLICATION_PATH", realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR 
       . "application"));

define("LIBRARY_PATH", realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . "library"));

define("SPARK_PATH", realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . 
  "lib" . DIRECTORY_SEPARATOR . "Spark" . DIRECTORY_SEPARATOR . "lib"));

set_include_path(LIBRARY_PATH . PATH_SEPARATOR . SPARK_PATH . PATH_SEPARATOR . get_include_path());

function __autoload($class)
{
  include_once str_replace("_", DIRECTORY_SEPARATOR, $class) . ".php";
}

$resolverOptions = array(
  "command_directory" => APPLICATION_PATH . DIRECTORY_SEPARATOR . "commands",
);

Spark_Object_Manager::addBinding(Spark_Object_OptionBinding::staticBind($resolverOptions)
  ->to("Spark_Controller_CommandResolver"));

$frontController = Spark_Object_Manager::getInstance("Spark_Controller_FrontController");

$router = $frontController->getRouter();

$router->addRoute("commands", new Zend_Controller_Router_Route(":command/:action/*", array("command"=>"Default", "action" => "default")));
$router->addRoute("pages", new Zend_Controller_Router_Route("/:name", array("command"=>"page", "name" => "index")));

$frontController->handleRequest();