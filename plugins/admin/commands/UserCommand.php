<?php

class Admin_UserCommand extends Spark_Controller_ActionController
{
  protected $_pages;
  
  public function beforeFilter($request, $response)
  {
    $this->_pages = new PageMapper;
    $this->_pages->setPagePath("plugins/admin/views");
  }
  
  public function indexAction($request, $response)
  {
    $response->appendBody($this->_pages->find("index")->content);
  }
  
  public function newAction($request, $response)
  {
    $response->appendBody($this->_pages->find("new")->content);
  }
  
  public function loginAction($request, $response)
  {
    $response->appendBody($this->_pages->find("login")->content);
  }
  
  public function logoutAction($request, $response)
  {
    
  }
  
}