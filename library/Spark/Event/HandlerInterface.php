<?php
/**
 * Interface so an Object can be registered as Handler
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
interface Spark_Event_HandlerInterface
{
  
  /**
   * This method gets called by the dispatcher when the event occurs
   * @param  Spark_Event $event Contains event information, such as 
   *                                     timestamp, message, event name
   * @return int The handler should return a status code (true/false, status code 
   *             constants from Spark_Event)
   */
  public function handleEvent($event);
  
}

?>