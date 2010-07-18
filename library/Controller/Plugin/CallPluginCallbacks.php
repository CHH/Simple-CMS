<?php

class Controller_Plugin_CallPluginCallbacks extends Spark_Controller_PluginAbstract
{
  
  protected $_plugins;
  
  public function __construct(PluginRegistry $registry) {
    $this->_plugins = $registry;
  }
  
  public function beforeDispatch($request, $response)
  {
    if($this->_plugins->has($this->_getPluginName($request))) {
      $this->_plugins->get($this->_getPluginName($request))->beforeDispatch();
    }
  }
  
  public function afterDispatch($request, $response)
  {
    if($this->_plugins->has($this->_getPluginName($request))) {
      $this->_plugins->get($this->_getPluginName($request))->afterDispatch();
    }
  }

  protected function _getPluginName($request)
  {
    return str_replace(" ", null, ucwords(str_replace("_", " ", $request->getModuleName())));
  }
  
}
