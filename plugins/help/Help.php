<?php

class Help extends Plugin
{

  public function bootstrap()
  {
    $router = $this->frontController->getRouter();

    $router->addRoute("help", new Zend_Controller_Router_Route("/gethelp/:topic/:page", array("module"=>"help", "command"=>"view", "topic"=>null, "page"=>"index")));

    $router->addRoute("helpDefault", new Zend_Controller_Router_Route("/gethelp/:page", array("module"=>"help", "command"=>"view", "topic"=>null, "page"=>"index")));
  }
  
  public function afterDispatch()
  {
    $this->layout->setLayoutPath(PLUGINS . "/help/public")
                 ->setLayoutName("layout.phtml");
  }
  
}
