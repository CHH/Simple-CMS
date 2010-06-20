<?php
/**
 * Interface for the Request, defines CommandName key for Request
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
interface Spark_Controller_RequestInterface
{
  
  public function getModuleName();
  public function setModuleName($value);
  
  public function getCommandName();
  public function setCommandName($value);
  
  public function getActionName();
  public function setActionName($value);
  
}