<?php

namespace Core\Plugin;

class Controller extends \Spark\Controller\ActionController
{
	protected $plugin;
	
	function init()
	{
		// init plugin
	}

	public function import($var)
	{
		return $this->plugin->import($var);
	}
}
