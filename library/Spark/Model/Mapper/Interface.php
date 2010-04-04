<?php

interface Spark_Model_Mapper_Interface
{
  
  public function init();
  
  public function find($id);
  
  public function findAll();
  
  public function save($entity);
  
  public function delete($id);
  
}
