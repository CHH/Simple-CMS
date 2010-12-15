<?php

namespace Plugin;

require_once "library/Page.php";
require_once "library/PageRoute.php";

use StdClass,
    Plugin\Pages\Page,
    Plugin\Pages\PageRoute,
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
		
		Event::observe($app, "post_dispatch", function($request, $response) use ($layout, $layoutRenderer) { 
		    $body = $response->getBody();
		    $layout->content = $body;
		    $response->setBody($layoutRenderer->render("layout", $layout));
		});
		
		Event::observe($app, "post_dispatch", array($this, "renderErrorPage"));
	}
	
	function renderErrorPage($request, $response)
	{
	    if (!$response->hasExceptions()) {
	        return;
	    }
	    
	    $exceptions = $response->getExceptions();
	    $exception = array_pop($exceptions);
        $code = $exception->getCode();
        
        if ($code == null or (ENVIRONMENT === "production" and $code > 500)) {
            $code = 500;
        }
        
        if($page = Page::find("_errors/{$code}")) {
            $page->code = $code;
            $page->message = $exception->getMessage();
            $page->stackTrace = $exception->getTrace();
            $page->requestedPage = $request->getParam("page");
            $page->exceptionType = get_class($exception);
            $page->requestUri = $request->getRequestUri();
            $page->requestMethod = $request->getMethod();
            
            $response->renderExceptions(false);
            return $response->append($page);
        }
        throw new Exception("No Error Page Template found");
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
