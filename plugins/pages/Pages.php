<?php

class Pages extends Plugin
{
  
  public function bootstrap()
  {
    $frontController = $this->import("FrontController");
    $request         = $frontController->getRequest(); 
    
    /**
     * Set up autoloading for the Libraries and Models of the plugin
     */
    spl_autoload_register(array($this, "autoloadPagesLibraries"));
    
    /**
     * Tell the Front Controller to use the error command of our plugin (=pages)
     */
    $frontController->setErrorCommand("pages::error");
    
    $frontController->getRouter()->addRoute(
      "pages", 
      Spark_Object_Manager::create(
        "PageRoute", 
        array("module_name"=>"pages")
      )
    );
    
    $layoutPath = $this->getPath() . "/default";
    
    if (isset($this->getConfig()->layout->path)) {
      $layoutPath = $this->getConfig()->layout->path;
    }
    
    /**
     * Create the layout Plugin for the Front Controller, it wraps 
     * all of our pages in a common layout template
     */
    $layoutPlugin = new Spark_Controller_Plugin_Layout(array(
      "layout_path" => $layoutPath
    ));
    
    /**
     * Add the Spark View Helpers (Gravatar, Link, Textile, HtmlElement,...) 
     * to the Layout
     */
    $layoutPlugin->getLayout()->addHelperPath(
      "Spark" . DIRECTORY_SEPARATOR . "View" . DIRECTORY_SEPARATOR . "Helper", 
      "Spark_View_Helper"
    );
    
    $frontController->addPlugin($layoutPlugin, array(Spark_Controller_FrontController::EVENT_AFTER_DISPATCH));
    
    /**
     * Set up the doctype and charset for the layout
     */
    $layout = $layoutPlugin->getLayout();
    
    $layout->doctype("HTML5");
    $layout->headMeta()->setCharset("UTF-8"); 
    
    /**
     * Add Less.js to Layout for CSS Preprocessing
     */
    $layout->headScript()->prependFile($request->getBaseUrl() . "/js/less.min.js");
    
    $layout->headLink()->prependStylesheet($request->getBaseUrl() . "/styles/reset.css");
    
    /**
     * If the App is in development mode, then prepend our stylesheet for pretty 
     * Errors and default pages
     */
    if (APPLICATION_ENVIRONMENT === "development") {  
      $layout->headLink(array(
        "href" => $request->getBaseUrl() . "/styles/defaults.less",
        "rel"  => "stylesheet/less",
        "type" => "text/css"
      ));
      $layout->headScript()->appendScript("less.env='development'; less.watch();");
    }
    
    PageMapper::setDefaultRenderer($layout);
    
    $this->export("Pages", new PageMapper);
    $this->export("LayoutPlugin", $layoutPlugin);
  }
  
  public function autoloadPagesLibraries($class)
  {
    @include_once(
      $this->getPath() . DIRECTORY_SEPARATOR . "library" . DIRECTORY_SEPARATOR
      . str_replace("_", DIRECTORY_SEPARATOR, $class) . ".php"
    );
  }
  
}
