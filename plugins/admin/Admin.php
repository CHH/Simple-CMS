<?php

class Admin extends Plugin
{
  public function bootstrap()
  {
    spl_autoload_register(array($this, "autoloadAuthModels"));
  }
  
  public function beforeDispatch()
  {
    $this->layoutPlugin->setLayoutPath($this->getPath() . "/views");
    $this->layoutPlugin->setLayoutName("layout.phtml");
    
    $layout = $this->layoutPlugin->getLayout();
    
    $layout->headScript()->prependFile("/js/jquery.min.js");
    $layout->headScript()->appendFile("/js/jquery.textchange.min.js");
    $layout->headScript()->appendFile("/js/admin_panel.js");
    
    $layout->placeholder("signature")->set("admin_panel");
    
    $layout->headLink(array(
      "href" => "/styles/admin_panel.less",
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
