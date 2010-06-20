<?php
/**
 * Base Class for Front Controller Plugins
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
 */
abstract class Spark_Controller_PluginAbstract implements Spark_Event_HandlerInterface
{
  
  final public function handleEvent($event)
  {
    if(!($event instanceof Spark_Controller_Event)) {
      return;
    }
    
    $eventName = explode(".", $event->getName());
    $method = str_replace(" ", "", ucwords(str_replace("_", " ", strtolower($eventName[sizeof($eventName) - 1]))));
    $method[0] = strtolower($method[0]);
    
    if(method_exists($this, $method)) {
      return $this->$method($event->getRequest(), $event->getResponse());
    }
  }
  
}