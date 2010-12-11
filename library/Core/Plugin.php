<?php

autoload('Core\Plugin\Exception',      __DIR__ . '/Plugin/Exception.php');
autoload('Core\Plugin\Controller',     __DIR__ . '/Plugin/Controller.php');
autoload('Core\Plugin\AbstractPlugin', __DIR__ . '/Plugin/AbstractPlugin.php');

require_once 'Plugin/Plugin.php';
require_once 'Plugin/Loader.php';
require_once 'Plugin/StandardLoader.php';
