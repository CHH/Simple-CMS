<?php

namespace Plugin;

autoload("Plugin\Pages\ErrorHandler", __DIR__ . "/library/ErrorHandler.php");

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
 * @todo Concept for errors
 */
class Pages extends \Core\Plugin\AbstractPlugin
{
	function init()
	{
		$routes = $this->import("Routes");
		$app    = $this->import("App");
		
		$pageRoute = new PageRoute(array(
		    "callback" => array($this, "render")
		));
		$routes->addRoute($pageRoute);
		
		Page::setSearchPath($this->getPath() . "/default");
		Page::setSearchPath(\Core\APPROOT . "/pages");
		
		$layout = new StdClass;
		$this->export("Layout", $layout);
		
        $layout->title = "Willkommen zu SimpleCMS!";    
	    
		$layoutRenderer = new Mustache;
		$layoutRenderer->setTemplatePath(\Core\APPROOT . "/layouts");
		$layoutRenderer->setTemplatePath($this->getPath() . "/default");
		
		$app->filter(function($request, $response) use ($layout, $layoutRenderer) { 
		    $body = $response->getBody();
		    $layout->content = $body;
		    $response->setBody($layoutRenderer->render("layout", $layout));
		});
		
		$app->filter(new ErrorHandler);
	}
	
	function render($request, $response)
	{
	    $page   = $request->getParam("page");
        $layout = $this->import("Layout");
        
        if (strpos($page, "_") === 0 or strpos($page, "/_") !== false) {
            throw new Exception("Page is hidden", 404);
        }
        $page = Page::find($page);
        $page->setAttribute("layout", $layout);
        $response->append($page->getContent());
	}
}
