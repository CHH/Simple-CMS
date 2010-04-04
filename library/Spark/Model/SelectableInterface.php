<?php

interface Spark_Model_SelectableInterface
{
  
  public function getSelect();
  
  public function findBySelect(Zend_Db_Select $select);
}
