<?php

class Controller_Plugin_CallPluginCallbacks 
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
  
  public function beforeDispatch($request, $response)
  {
    $plugin = $this->_getPluginName($request);
    if($this->_plugins->has($plugin)) {
      $this->_plugins->get($plugin)->beforeDispatch($request, $response);
    }
  }
  
  public function afterDispatch($request, $response)
  {
    $plugin = $this->_getPluginName($request);
    if($this->_plugins->has($plugin)) {
      $this->_plugins->get($plugin)->afterDispatch($request, $response);
    }
  }
  
  public function setPlugins(PluginRegistry $plugins)
  {
    $this->_plugins = $plugins;
    return $this;
  }
  
  protected function _getPluginName($request)
  {
    return str_replace(" ", null, ucwords(str_replace("_", " ", $request->getModuleName())));
  }
  
}
