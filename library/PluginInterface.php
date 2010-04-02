<?php

interface PluginInterface
{
  /**
   * bootstrap() - Gets called from the main bootstrap at plugin load time
   */
  public function bootstrap();
  
  /** 
   * setFrontController() - Provides an instance of the FrontController to the Plugin
   * @var Spark_Controller_FrontController $frontController
   * @return PluginInterface Should provide a fluent interface
   */
  public function setFrontController(Spark_Controller_FrontController $frontController);
  
  /**
   * setLayoutFilter() - Provides an instance of the Layout to the Plugin,
   * commonly used to change the Layout, set placeholders, add View Helpers,...
   *
   * @param Spark_Controller_Filter_ApplyLayout $layoutFilter
   * @return PluginInterface Should provide a fluent interface
   */
  public function setLayoutFilter(Spark_Controller_Filter_ApplyLayout $layoutFilter);
  
  /**
   * set() - Sets a key value pair to the Plugin
   *
   * @param string $var
   * @param mixed $value
   * @return PluginInterface Should provide a fluent interface
   */
  public function set($var, $value);
  
  /**
   * get() - Gets the value for a given key
   *
   * @param string $var
   * @return mixed
   */
  public function get($var);
  
  /**
   * setConfig() - Sets a plugin specific config
   *
   * @param mixed $config Config file, should either be an array or an instance of Zend_Config
   * @return PluginInterface Should provide a fluent interface
   */
  public function setConfig($config);
  
  /**
   * getConfig() - Returns the plugin config
   *
   * @return array
   */
  public function getConfig();
  
}