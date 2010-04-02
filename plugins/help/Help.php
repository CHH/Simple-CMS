<?php

class Help extends Plugin
{

  public function bootstrap($config = null)
  {
    $router = $this->getFrontController()->getRouter();

    $router->addRoute("help", new Zend_Controller_Router_Route("/gethelp/:topic/:page", array("module"=>"help", "command"=>"view", "topic"=>null, "page"=>"index")));

    $router->addRoute("helpDefault", new Zend_Controller_Router_Route("/gethelp/:page", array("module"=>"help", "command"=>"view", "topic"=>null, "page"=>"index")));
  }

}
