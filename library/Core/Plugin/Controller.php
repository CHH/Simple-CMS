<?php

namespace Core\Plugin;

class Controller extends \Spark\Controller\ActionController
{
	static protected $environment;
	
	static function setEnvironment(Environment $env)
	{
	    static::$environment = $env;
	}

	public function import($var)
	{
		return static::$environment->import($var);
	}
}
