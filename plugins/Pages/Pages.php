<?php
/**
 * Pages Plugin
 *
 * Renders requested pages and handles the layout
 * 
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @package    Plugin
 * @subpackage Pages
 * @author     Christoph Hochstrasser <christoph.hochstrasser@gmail.com>
 * @copyright  Copyright (c) 2010 Christoph Hochstrasser
 * @license    MIT License
 */
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
    Spark\Event,
    Spark\Util;

/**
 * @todo Allow rendering of pages from within pages
 * @todo Implement Page::findAll properly
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
        $config = $this->import("Config");
        
        $routes->addRoute(new PageRoute(array($this, "render")));
        
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

        $layoutName = isset($config["Pages"]["layout_name"]) 
            ? $config["Pages"]["layout_name"] 
            : "layout";
        
        // Render the layout after the dispatching process
        $app->after(function($request, $response) use ($layoutName, $layout, $layoutRenderer) { 
            $body = $response->getBody();
            $layout->content = $body;
            $response->setBody($layoutRenderer->render($layoutName, $layout));
        });
        
        // Checks if the response has errors and renders appropiate error pages
        $app->error(new ErrorHandler);
    }
    
    function render($request, $response)
    {
        $config = $this->import("Config");
        $config = Util\array_delete_key("Pages", $config) ?: array();
        
        $renderTextile = isset($config["render_textile"]) ? $config["render_textile"] : true;
        
        $page = $request->meta("page");
        
        if (strpos($page, "_") === 0 or strpos($page, "/_") !== false) {
            throw new \Spark\Controller\Exception("Page is hidden", 404);
        }
        $page = Page::find($page);
        $page->setRenderTextile($renderTextile);
        $response->append($page->getContent());
    }
}
