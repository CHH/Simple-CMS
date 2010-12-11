<?php
/**
 * This is the core plugin file, the only file thats truly required
 * for a plugin to work.
 */
namespace Plugin;

class Sample extends \Core\Plugin\AbstractPlugin
{
    /**
     * This method gets automatically called when the App is set up.
     * That means, all components are configured and your plugin just gets 
     * loaded by the Plugin Loader. 
     */
    public function init() 
    {
    	echo "foo";
    }
  	
    /**
     * This method gets called by the Front Controller if your plugin gets
     * requested, but before a command or action controller of the plugin
     * gets called. Use this e.g. to set a custom layout for the frontend
     * of your plugin.
     *
     * Performance wise, it's also a good idea to do the heavy lifting in
     * this callback, to keep the bootstrap() method light and not drag
     * down application bootstrap performance for initializing components
     * which are never needed if a command/controller gets not requested
     */
    public function preDispatch($request, $response)
    {}
  
    /**
     * This method gets called by the Front Controller after the command or
     * Action Controller got rendered and before the output of your 
     * Command/Controllers gets sent to the User.
     *
     * You can also use this to do some cleanup work on the end of the 
     * plugin lifecycle.
     */
    public function postDispatch($request, $response)
    {}
}
