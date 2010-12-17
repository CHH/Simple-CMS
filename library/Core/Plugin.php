<?php
/**
 * Includes all libaries related to plugin loading
 * 
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @author     Christoph Hochstrasser <christoph.hochstrasser@gmail.com>
 * @copyright  Copyright (c) 2010 Christoph Hochstrasser
 * @license    MIT License
 */
autoload('Core\Plugin\Exception',      __DIR__ . '/Plugin/Exception.php');
autoload('Core\Plugin\ExceptionStack', __DIR__ . '/Plugin/ExceptionStack.php');
autoload('Core\Plugin\Controller',     __DIR__ . '/Plugin/Controller.php');
autoload('Core\Plugin\AbstractPlugin', __DIR__ . '/Plugin/AbstractPlugin.php');

require_once 'Plugin/Plugin.php';
require_once 'Plugin/Loader.php';
require_once 'Plugin/Environment.php';
require_once 'Plugin/StandardLoader.php';
