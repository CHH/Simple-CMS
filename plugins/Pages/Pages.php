<?php

namespace Plugin;

autoload("Plugin\Pages\ErrorHandler", __DIR__ . "/library/ErrorHandler.php");

require_once "library/Pragma.php";
require_once "library/Page.php";
require_once "library/PageRoute.php";

use StdClass,
    Plugin\Pages\Page,
    Plugin\Pages\PageRoute,
    Plugin\Pages\ErrorHandler,
    Phly\Mustache\Mustache,
    Spark\Event;

/**
 * @todo Allow rendering of pages from within pages
 * @todo Implement findAll properly
 */
class Pages extends \Core\Plugin\AbstractPlugin
{
    /**
     * Inits the route for page rendering, registers a layout renderer and 
     * provides fancy error pages
     *
     * @return void
     */
    function init()
    {
        $routes = $this->import("Routes");
        $app    = $this->import("App");
        
        $pageRoute = new PageRoute(array(
            "callback" => array($this, "render")
        ));
        $routes->addRoute($pageRoute);
        
        Page::setSearchPath(array(
            $this->getPath() . "/default",
            \Core\APPROOT    . "/pages"
        ));
        
        // An object which holds the layout variables
        $layout = new StdClass;
        $this->export("Layout", $layout);

        $layout->title = "Willkommen zu SimpleCMS!";    
        
        $layoutRenderer = new Mustache;
        $layoutRenderer->setTemplatePath($this->getPath() . "/default")
                       ->setTemplatePath(\Core\APPROOT    . "/layouts");
        
        // Render the layout after the dispatching process
        $app->postDispatch(function($request, $response) use ($layout, $layoutRenderer) { 
            $body = $response->getBody();
            $layout->content = $body;
            $response->setBody($layoutRenderer->render("layout", $layout));
        });
        
        // Checks if the response has errors and renders appropiate error pages
        $app->postDispatch(new ErrorHandler);
    }

    function render($request, $response)
    {
        $page   = $request->getParam("page");
        
        if (strpos($page, "_") === 0 or strpos($page, "/_") !== false) {
            throw new \Spark\Controller\Exception("Page is hidden", 404);
        }
        $page = Page::find($page);
        $response->append($page->getContent());
    }
}
