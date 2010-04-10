<?php

interface Spark_Controller_RequestInterface
{
  
  public function getModuleName();
  public function setModuleName($value);
  
  public function getCommandName();
  public function setCommandName($value);
  
  public function getActionName();
  public function setActionName($value);
  
}