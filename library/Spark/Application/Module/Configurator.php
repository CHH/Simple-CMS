<?php

class Spark_Application_Module_Configurator
{
  /**
   * Bootstrap instance
   * @var Zend_Application_Bootstrap_Bootstrapper
   */
  protected $_bootstrap;
  
  /**
   * Module configuration
   * @var Zend_Config
   */
  protected $_config;
  
  /** Namespace for configurator classes
   * @var string
   */
  protected $_configuratorNamespace = "Spark_Application_Module_Configurator_";
  
  
  public function __construct($bootstrap, 
    Zend_Config $config)
  {
    $this->_bootstrap = $bootstrap;
    $this->_config = $config;
  }
  
  public function run()
  {
    $resources = $this->_config->resources->toArray();
    
    foreach($resources as $resourceName => $options) {
      $resourceClass = $this->_configuratorNamespace . ucfirst($resourceName);
      $resource = new $resourceClass($options);
      
      $resource->setBootstrap($this->_bootstrap);
      $resource->init();
      
    }
    
  }
  
  public function setConfiguratorNamespace($namespace)
  {
    if(!is_string($namespace)) {
      throw new InvalidArgumentException("Namespace must be of type String");
    }
    
    if( $namespace[strlen($namespace)] !== "_" ) {
      $namespace .= "_";
    }
    $this->_configuratorNamespace = $namespace;
  }
  
  public function getConfiguratorNamespace()
  {
    return $this->_configuratorNamespace;
  }
  
}

?>
