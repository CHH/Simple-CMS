<?php

class Spark_Controller_Plugin_ModuleConfigurator extends Zend_Controller_Plugin_Abstract
{
  
  public function preDispatch(Zend_Controller_Request_Abstract $request)
  {
    $frontController = Zend_Controller_Front::getInstance();
    $bootstrap = $frontController->getParam("bootstrap");
    $moduleName = $request->getModuleName();
    $moduleDirectory = $frontController->getModuleDirectory($moduleName);
    $configPath = $moduleDirectory . "/configs/module.ini";
    
    if(file_exists($configPath)) {
      
      if(is_readable($configPath)) {
        $config = new Zend_Config_Ini($configPath, $bootstrap->getEnvironment());
        
        $configurator = new Spark_Application_Module_Configurator($bootstrap, $config);
        $configurator->run();
        
      } else {
        throw new Spark_Exception("module.ini is not readable for module " .
          $moduleName);
      } 
      
    }
  } 
  
}

?>
