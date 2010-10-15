<?php

class Controller_Plugin_PluginCallbacks 
    extends Spark_Controller_AbstractPlugin
    implements Spark_Configurable
{
    protected $_plugins;

    public function __construct(Array $options = array()) 
    {
       $this->setOptions($options);
    }

    public function setOptions(Array $options)
    {
        Spark_Options::setOptions($this, $options);
        return $this;
    }

    public function preDispatch($request, $response)
    {
        $plugin = $this->_getPluginName($request);

        if($this->_plugins->has($plugin)) {
            $plugin = $this->_plugins->get($plugin);

            if (!is_callable(array($plugin, "preDispatch"))) {
                return;
            }
            $plugin->preDispatch($request, $response);
        }
    }

    public function postDispatch($request, $response)
    {
        $plugin = $this->_getPluginName($request);

        if($this->_plugins->has($plugin)) {
            $plugin = $this->_plugins->get($plugin);

            if (!is_callable(array($plugin, "postDispatch"))) {
                return;
            }

            $plugin->postDispatch($request, $response);
        }
    }

    public function setPlugins(Spark_Registry $plugins)
    {
        $this->_plugins = $plugins;
        return $this;
    }

    protected function _getPluginName($request)
    {
        return str_replace(" ", null, ucwords(str_replace("_", " ", $request->getModuleName())));
    }
}