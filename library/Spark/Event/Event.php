<?php
/**
 * Base Event Class
 * 
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @category   Spark
 * @package    Spark_Event
 * @author     Christoph Hochstrasser <christoph.hochstrasser@gmail.com>
 * @copyright  Copyright (c) 2010 Christoph Hochstrasser
 * @license    MIT License
 */

/**
 * @category   Spark
 * @package    Spark_Event
 * @copyright  Copyright (c) 2010 Christoph Hochstrasser
 * @license    MIT License
 */
class Spark_Event_Event
{
  
  /**
   * Name of the event
   * @var string
   */
  protected $_name;
  
  /**
   * Context in which event got triggered
   * @var object
   */
  protected $_context = null;
  
  /**
   * Timestamp for the occurence of the event
   * @var int
   */
  protected $_timestamp;
  
  protected $_dispatched = false;
  
  /**
   * Constructor
   * @param  string $event
   * @param  mixed  $message
   * @param  int    $timestamp
   *
   * @return Spark_Event_Event
   */
  public function __construct()
  {
    $this->_timestamp = time();
  }
  
  /**
   * Sets the event name
   * @param  string $eventName
   * @return Spark_Event_Event Providing a fluent interface
   */
  public function setName($eventName)
  {
    $this->_name = $eventName;
    return $this;
  }
  
  /**
   * Returns the name of the event
   * @return string
   */
  public function getName()
  {
    return $this->_name;
  }
  
  public function hasName()
  {
    return ($this->_name != null) ? true : false;
  }
  
  public function setContext($context)
  {
    $this->_context = $context;
    return $this;
  }
  
  public function getContext()
  {
    return $this->_context;
  }
  
  public function hasContext()
  {
    return ($this->_context !== null) ? true : false;
  }
  
  public function setDispatched($dispatched = true)
  {
    $this->_dispatched = $dispatched ? true : false;
    return $this;
  }
  
  public function isDispatched()
  {
    return $this->_dispatched;
  }
  
  /**
   * Sets the timestamp of the occurence of the event
   * @param  int $timestamp
   * @return Spark_Event_Event Providing a fluent interface
   */
  protected function _setTimestamp($timestamp)
  {
    $this->_timestamp = $timestamp;
    return $this;
  }
  
  /**
   * Returns the timestamp of the event occurence
   * @return int
   */
  public function getTimestamp()
  {
    return $this->_timestamp;
  }
  
  
}

?>