<?php

class Pages extends Plugin
{
  
  public function bootstrap()
  {
    spl_autoload_register(array($this, "autoloadPluginLibrary"));
    spl_autoload_register(array($this, "autoloadPluginModel"));
    
    $this->frontController->getRouter()->addRoute("pages", Spark_Object_Manager::create("PageRoute", array("module_name"=>"pages")));
    
    $this->layout->getLayout()->registerHelper(new View_Helper_Pages, "pages");
  }
  
  public function afterDispatch()
  {
    
  }
  
  public function autoloadPluginLibrary($class)
  {
    @include_once($this->getPath() . DIRECTORY_SEPARATOR . "library" . 
       DIRECTORY_SEPARATOR . str_replace("_", DIRECTORY_SEPARATOR, $class) . ".php");
  }
  
  public function autoloadPluginModel($class)
  {
     @include_once($this->getPath() . DIRECTORY_SEPARATOR . "models" . 
       DIRECTORY_SEPARATOR . str_replace("_", DIRECTORY_SEPARATOR, $class) . ".php");
  }
  
}