<?php

$router = Spark_Registry::get("FrontController")->getRouter();

$router->addRoute("help", new Zend_Controller_Router_Route("/gethelp/:topic/:page", array("module"=>"help", "command"=>"help", "topic"=>null, "page"=>"index")));

$router->addRoute("helpDefault", new Zend_Controller_Router_Route("/gethelp/:page", array("module"=>"help", "command"=>"help", "topic"=>null, "page"=>"index")));

