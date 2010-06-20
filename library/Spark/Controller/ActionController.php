<?php
/**
 * Basic Implementation of an Action Controller. Allows to request methods directly
 * from an url, e.g. a request to the action "foo" gets delegated to a Method
 * called fooAction().
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
abstract class Spark_Controller_ActionController implements Spark_Controller_CommandInterface
{
  
  /**
   * Gets called by the Front Controller on dispatch
   *
   * @param  Spark_Controller_RequestInterface $request
   * @param  Zend_Controller_Response_Abstract $response
   * @return void
   */
  public function execute(
    Spark_Controller_RequestInterface $request,
    Zend_Controller_Response_Abstract $response
  )
  {
    $action = $request->getActionName();
    
    if($action == null) {
      $action = "index";
    }
    
    $method = str_replace(" ", "", ucwords(str_replace("_", " ", str_replace("-", " ", strtolower($action)))));
    $method[0] = strtolower($method[0]);
    
    $method = $action . "Action";
    
    if(method_exists($this, $method)) {
      $this->$method($request, $response);
    } else {
      $controller = get_class($this);
      throw new Spark_Controller_Exception("The action {$action} was not found in
        the controller {$controller}. Please make sure the method {$method} exists.", 404);
    }
  }
}