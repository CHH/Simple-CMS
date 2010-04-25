<?php

class Rest extends Plugin
{
  
  public function bootstrap()
  {
    
    require_once($this->getPath() . "/library/RestRoute.php");
    
    $restRoute = new RestRoute;
    
    $this->frontController->getRouter()->addRoute("api", $restRoute);
  }
  
}