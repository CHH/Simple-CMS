<?php

interface Spark_Model_FilterChain_Interface
{
  
  public function addFilter(Spark_Model_Filter_Interface $filter);
  public function processFilters(Spark_Model_Entity $entity);
  
}
