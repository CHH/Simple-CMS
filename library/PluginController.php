<?php

class PluginController extends Spark_Controller_ActionController
{
  protected $_plugin;
  
  public function getPlugin()
  {
    if (is_null($this->_plugin)) {
      $this->_plugin = Spark_Registry::get("Plugins")
                         ->get(str_replace(" ", null, ucwords(str_replace("_", " ", $this->_request->getModuleName()))));
    }
    return $this->_plugin;
  }
  
  public function import($var)
  {
    return $this->getPlugin()->import($var);
  }
}
