<?php

namespace Core\Plugin;

class Controller extends \Spark\Controller\ActionController
{
	static protected $environment;
	protected $plugin;
	
	static function setEnvironment(Environment $env)
	{
	    static::$environment = $env;
	}
    
    protected function getPlugin()
    {
        if (null === $this->plugin) {
            $pluginName = str_camelize($this->request->getParam("module"));
            $this->plugin = static::$environment->getPlugin($pluginName);
        }
        return $this->plugin;
    }
    
	protected function import($var)
	{
		return static::$environment->import($var);
	}
}
