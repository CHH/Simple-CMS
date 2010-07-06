<?php

class Pages extends Plugin
{
  
  public function bootstrap()
  {
    spl_autoload_register(array($this, "autoloadPagesLibraries"));
    spl_autoload_register(array($this, "autoloadPagesModels"));
    
    $this->frontController->setErrorCommand("pages::error");
    
    $this->frontController->getRouter()->addRoute(
      "pages", 
      Spark_Object_Manager::create(
        "PageRoute", 
        array("module_name"=>"pages")
      )
    );
    
    $this->layoutPlugin->setLayoutPath($this->getPath() . DIRECTORY_SEPARATOR . "default");
    $this->layoutPlugin->setLayoutName("layout.phtml");
        
    $layout = $this->layoutPlugin->getLayout();
    
    $layout->doctype("HTML5");
    $layout->headMeta()->setCharset("UTF-8"); 
    
    $layout->headScript()->prependFile("/js/less.min.js");
    
    /**
     * If the App is in development mode, then prepend our stylesheet for pretty 
     * Errors and default pages
     */
    if (APPLICATION_ENVIRONMENT === "development") {  
      $layout->headLink(array(
        "href" => "/styles/defaults.less",
        "rel"  => "stylesheet/less",
        "type" => "text/css"
      ));
      $layout->headScript()->appendScript("less.env='development'; less.watch();");
    }
    
    PageMapper::setDefaultRenderer($layout);
  }
  
  public function autoloadPagesLibraries($class)
  {
    @include_once(
      $this->getPath() . DIRECTORY_SEPARATOR . "library" . DIRECTORY_SEPARATOR
      . str_replace("_", DIRECTORY_SEPARATOR, $class) . ".php"
    );
  }
  
  public function autoloadPagesModels($class)
  {
    @include_once(
      $this->getPath() . DIRECTORY_SEPARATOR . "models" . DIRECTORY_SEPARATOR 
      . str_replace("_", DIRECTORY_SEPARATOR, $class) . ".php"
    );
  }
  
}
