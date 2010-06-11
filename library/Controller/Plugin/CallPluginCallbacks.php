<?php

class Controller_Plugin_CallPluginCallbacks extends Spark_Controller_PluginAbstract
{
  
  protected $_plugins;
  
  public function __construct(PluginRegistry $registry) {
    $this->_plugins = $registry;
  }
  
  public function beforeDispatch($request, $response)
  {
    if($this->_plugins->has($request->getModuleName())) {
      $this->_plugins->get($request->getModuleName())->beforeDispatch();
    }
  }
  
  public function afterDispatch($request, $response)
  {
    if($this->_plugins->has($request->getModuleName())) {
      $this->_plugins->get($request->getModuleName())->afterDispatch();
    }
  }
  
}
