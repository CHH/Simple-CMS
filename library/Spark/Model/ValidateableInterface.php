<?php

interface Spark_Model_ValidateableInterface
{
  public function isValid();
  public function getInvalid();
}