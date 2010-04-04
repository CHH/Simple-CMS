<?php

class Spark_Application_Module_Configurator_Layout
  extends Zend_Application_Resource_ResourceAbstract
{
  
  public function init()
  {
    $layout = new Zend_Application_Resource_Layout($this->getOptions());
    $layout->setBootstrap($this->getBootstrap());
    $layout->init();
  }
  
}

?>
