<?php

class Pages extends AbstractPlugin
{
    public function init()
    {
        $frontController = $this->import("FrontController");
        $request         = $frontController->getRequest(); 
        
        // Register an Autoloader for the classes in the plugin's library path
        $loader = new Autoloader(array("include_path" => $this->getPath() . "/library"));
        $loader->register();
        
        // Use the error controller of our plugin (=pages)
        $frontController->setErrorController(array("pages", "error"));
        
        $pageRoute = new PageRoute(array(
            "module_name"     => "pages", 
            "controller_name" => "page"
        ));
        
        $frontController->getRouter()->addRoute("pages", $pageRoute);
        
        /*
         * Create the layout Plugin for the Front Controller, it wraps 
         * all of our pages in a common layout template
         */
        $layoutPlugin = new Spark_Controller_Plugin_Layout;
        $layoutPlugin->setLayoutPath($this->getPath() . "/default");
        
        $layout = $layoutPlugin->getLayout();
        $layout->addScriptPath(APPROOT . "/layouts");
        
        $frontController->addPlugin(
            $layoutPlugin,
            array(Spark_Controller_FrontController::EVENT_POST_DISPATCH)
        );
        
        /*
         * Add the Spark View Helpers (Gravatar, Link, Textile, HtmlElement,...) 
         * to the Layout
         */    
        $layout->addHelperPath(
          "Spark" . DIRECTORY_SEPARATOR . "View" . DIRECTORY_SEPARATOR . "Helper", 
          "Spark_View_Helper"
        );

        // Set up the doctype and charset for the layout
        $layout->doctype("HTML5");
        $layout->headMeta()->setCharset("UTF-8"); 

        // Add Less.js to Layout for CSS Preprocessing
        $layout->headScript()->prependFile($request->getBaseUrl() . "/javascript/less.min.js");
        $layout->headLink()->prependStylesheet($request->getBaseUrl() . "/styles/reset.css");

        Page::setSearchPath($this->getPath() . DIRECTORY_SEPARATOR . "default");
        Page::setSearchPath(APPROOT . DIRECTORY_SEPARATOR . "pages");
        
        /*
         * If the App is in development mode, then prepend our stylesheet for pretty 
         * errors and default pages
         */
        if (ENVIRONMENT === "development") {  
            $layout->headLink(array(
                "href" => $request->getBaseUrl() . "/styles/defaults.less",
                "rel"  => "stylesheet/less",
                "type" => "text/css"
            ));
            $layout->headScript()->appendScript("less.env='development'; less.watch();");
        }
        
        $this->export("LayoutPlugin", $layoutPlugin);
    }

    public function preDispatch($request, $response)
    {
        $page = $request->getParam("page");
        
        if (strpos($page, "_") === 0 or strpos($page, "/_") !== false) {
            throw new Exception("Page is hidden", 404);
        }
        
        $page = Page::find($page);
        $page->setAttribute("layout", $this->import("LayoutPlugin")->getLayout());
        
        $response->appendBody($page->getContent());
        $request->setDispatched(true);
    }
}