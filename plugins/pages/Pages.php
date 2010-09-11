<?php

class Pages extends AbstractPlugin
{
  
  public function init()
  {
    $frontController = $this->import("FrontController");
    $request         = $frontController->getRequest(); 
    
    /*
     * Set up autoloading for the Libraries and Models of the plugin
     */
    spl_autoload_register(array($this, "autoloadPagesLibraries"));
    
    /*
     * Tell the Front Controller to use the error command of our plugin (=pages)
     */
    $frontController->setErrorController("pages::error");
    
    $frontController->getRouter()->addRoute(
      "pages", 
      new PageRoute(
        array("module_name" => "pages", "controller_name" => "page")
      )
    );
    
    /*
     * Create the layout Plugin for the Front Controller, it wraps 
     * all of our pages in a common layout template
     */
    $layoutPlugin = new Spark_Controller_Plugin_Layout;
    $frontController->addPlugin($layoutPlugin, array(Spark_Controller_FrontController::EVENT_AFTER_DISPATCH));
    
    $layoutPlugin->setLayoutPath($this->getPath() . "/default");

    $layout = $layoutPlugin->getLayout();

    $layout->addScriptPath(APPROOT . "/layouts");
    $layout->addScriptPath(APPROOT);

    Page::setRenderer($layout);
    
    /*
     * Add the Spark View Helpers (Gravatar, Link, Textile, HtmlElement,...) 
     * to the Layout
     */    
    $layout->addHelperPath(
      "Spark" . DIRECTORY_SEPARATOR . "View" . DIRECTORY_SEPARATOR . "Helper", 
      "Spark_View_Helper"
    );
    
    /*
     * Set up the doctype and charset for the layout
     */
    $layout->doctype("HTML5");
    $layout->headMeta()->setCharset("UTF-8"); 
    
    /*
     * Add Less.js to Layout for CSS Preprocessing
     */
    $layout->headScript()->prependFile($request->getBaseUrl() . "/javascript/less.min.js");
    
    $layout->headLink()->prependStylesheet($request->getBaseUrl() . "/styles/reset.css");
    
    /*
     * If the App is in development mode, then prepend our stylesheet for pretty 
     * Errors and default pages
     */
    if (ENVIRONMENT === "development") {  
      $layout->headLink(array(
        "href" => $request->getBaseUrl() . "/styles/defaults.less",
        "rel"  => "stylesheet/less",
        "type" => "text/css"
      ));
      $layout->headScript()->appendScript("less.env='development'; less.watch();");
    }
    
    $this->export("Pages", new PageMapper);
    $this->export("LayoutPlugin", $layoutPlugin);
  }

  public function beforeDispatch($request, $response)
  {
    $page = $request->getParam("page");
    
    if (strpos($page, "_") === 0 or strpos($page, "/_") !== false) {
      throw new Exception("Page is hidden", 404);
    }
    
    $page = Page::find($page);
    
    $response->appendBody($page->content);
    
    $request->setDispatched(true);
  }
  
  public function autoloadPagesLibraries($class)
  {
    include_once(
      $this->getPath() . DIRECTORY_SEPARATOR . "library" . DIRECTORY_SEPARATOR
      . str_replace("_", DIRECTORY_SEPARATOR, $class) . ".php"
    );
  }
  
}
