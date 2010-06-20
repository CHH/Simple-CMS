<?php
/**
 * HTTP Request
 * 
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @category   Spark
 * @package    Spark_Controller
 * @author     Christoph Hochstrasser <christoph.hochstrasser@gmail.com>
 * @copyright  Copyright (c) 2010 Christoph Hochstrasser
 * @license    MIT License
 */

/**
 * @category   Spark
 * @package    Spark_Controller
 * @copyright  Copyright (c) 2010 Christoph Hochstrasser
 * @license    MIT License
 * @see        Zend_Controller_Request_Http
 */
class Spark_Controller_HttpRequest
  extends Zend_Controller_Request_Http implements Spark_Controller_RequestInterface
{
  
  protected $_commandName;
  protected $_commandKey = "command";
  
  public function getCommandName()
  {
    if(is_null($this->_commandName)) {
      $this->_commandName = $this->getParam($this->getCommandKey());
    }
    
    return $this->_commandName;
  }
  
  public function setCommandName($value)
  {
    $this->_commandName = $value;
    return $this;
  }
  
  public function getCommandKey()
  {
    return $this->_commandKey;
  }
  
  public function setCommandKey($key)
  {
    $this->_commandKey = $key;
    return $this;
  }
  
}