<?php
/**
 * This is the core plugin file, the only file thats truly required
 * for a plugin to work.
 */
namespace Plugin;

use Spark\Router\Redirect;

class Sample extends \Core\Plugin\AbstractPlugin
{
    /**
     * This method gets automatically called when the App is set up.
     * That means, all components are configured and your plugin just gets 
     * loaded by the Plugin Loader. 
     */
    function init() 
    {
        $routes = $this->import("Routes");
        
        $routes->scope("sample", function($sample) {
            $sample->match(array("/" => "index#index"));
            $sample->match(array("/redirect_test" => new Redirect("http://google.at")));
        });
    }
}
