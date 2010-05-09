<?php

class Pages extends Plugin
{
  
  public function bootstrap()
  {
    spl_autoload_register(array($this, "autoloadPagesLibraries"));
    spl_autoload_register(array($this, "autoloadPagesModels"));
    
    $this->frontController->getRouter()->addRoute(
      "pages", 
      Spark_Object_Manager::create("PageRoute", array("module_name"=>"pages"))
    );
    
    PageMapper::setDefaultRenderer($this->layout->getLayout());
  }
  
  public function autoloadPagesLibraries($class)
  {
    @include_once($this->getPath() . DIRECTORY_SEPARATOR . "library" . 
       DIRECTORY_SEPARATOR . str_replace("_", DIRECTORY_SEPARATOR, $class) . ".php");
  }
  
  public function autoloadPagesModels($class)
  {
     @include_once($this->getPath() . DIRECTORY_SEPARATOR . "models" . 
       DIRECTORY_SEPARATOR . str_replace("_", DIRECTORY_SEPARATOR, $class) . ".php");
  }
  
}
