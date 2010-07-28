<?php

class Controller_Plugin_CallPluginCallbacks extends Spark_Controller_PluginAbstract
{
  
  protected $_plugins;
  
  public function __construct(array $options = array()) 
  {
    $this->setOptions($options);
  }
  
  public function setOptions(array $options)
  {
    Spark_Object_Options::setOptions($this, $options);
    return $this;
  }
  
  public function beforeDispatch($request, $response)
  {
    if($this->_plugins->has($this->_getPluginName($request))) {
      $this->_plugins->get($this->_getPluginName($request))->beforeDispatch($request, $response);
    }
  }
  
  public function afterDispatch($request, $response)
  {
    if($this->_plugins->has($this->_getPluginName($request))) {
      $this->_plugins->get($this->_getPluginName($request))->afterDispatch($request, $response);
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
