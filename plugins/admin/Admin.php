<?php

class Admin extends Plugin
{
  public function bootstrap()
  {
    $this->dependOn("pages");
  }
  
  public function beforeDispatch()
  {
    spl_autoload_register(array($this, "autoloadAuthModels"));
    
    $layoutPlugin    = $this->import("LayoutPlugin");
    $frontController = $this->import("FrontController");
    $request         = $frontController->getRequest();
    
    $layoutPlugin->setLayoutName("admin.phtml");
    
    $layout = $layoutPlugin->getLayout();
    
    $layout->addScriptPath($this->getPath() . "/views");
    
    $layout->headScript()->prependFile($request->getBaseUrl() . "/js/jquery.min.js");
    $layout->headScript()->appendFile($request->getBaseUrl()  . "/js/jquery.textchange.min.js");
    $layout->headScript()->appendFile($request->getBaseUrl()  . "/js/admin_panel.js");
    
    $layout->placeholder("signature")->set("admin_panel");
    
    $layout->headLink(array(
      "href" => $request->getBaseUrl() . "/styles/admin_panel.less",
      "type" => "text/css",
      "rel"  => "stylesheet/less"
    ));
  }
  
  public function autoloadAuthModels($model)
  {
    @include_once $this->getPath() . DIRECTORY_SEPARATOR . "models" . DIRECTORY_SEPARATOR
                  . str_replace("_", DIRECTORY_SEPARATOR, $model);
  }
  
}
