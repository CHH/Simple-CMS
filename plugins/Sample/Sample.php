<?php

/**
 * This is the core plugin file, the only file thats truly required
 * for a plugin to work. 
 * 
 * The Convention is that the class name is the
 * name of the directory this file resides in, also refered to as
 * "Plugin ID", in CamelCase, for example my_super_plugin_id becomes
 * the classname MySuperPluginId.
 * 
 * Plugins can either extend the Plugin Abstract, 
 * called "Plugin" as in this file, or if you can implement the Plugin 
 * Interface directly if you are in need of more speed. Please read
 * the documentation of these classes if you want to learn more.
 *
 * You can access the Front Controller from within the plugin
 * by accessing the "frontController" property (e.g. to add routes for
 * your plugin). Additionally you can set all sorts of params by setting
 * them either with setOption() or directly as property on the plugin.
 * 
 * You have something that must be user configured for this plugin
 * to work? Then add a text file named "plugin.ini" to a directory named
 * "config" in your plugin root. This file is automatically loaded and
 * you can access it as object with the getConfig() method.
 *
 * To respond to HTTP Requests you must add at least a class named 
 * "DefaultController" in the "controllers" directory. The default controller 
 * must either implement the Spark_Controller_Controller Interface or
 * extend the abstract class Spark_Controller_ActionController.
 * The default route to your Plugin's controllers is
 * /:pluginId/:controller/:action If no controller parameter can be determined in
 * the Route, the Default Controller is called. 
 * Please read the documentation of Spark_Controller_ActionController
 * and Spark_Controller_Controller if you want to learn more.
 */
class Sample extends AbstractPlugin
{
  
    /**
     * This method gets automatically called when the App is set up.
     * That means, all components are configured and your plugin just gets 
     * loaded by the Plugin Loader. 
     */
    public function init() 
    {}
  
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