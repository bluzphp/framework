<?php
// Environment
define('DEBUG', true);
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 1);

// Paths
define('PATH_ROOT', realpath(dirname(__FILE__). '/../../../../../../'));
define('PATH_APPLICATION', realpath(dirname(__FILE__). '/../../'));
define('PATH_DATA', PATH_ROOT . '/data');
define('PATH_VENDOR', PATH_ROOT . '/vendor');
define('PATH_BLUZ', PATH_ROOT . '/vendor/bluzphp/framework/src/Bluz');
define('PATH_PUBLIC', PATH_ROOT . '/public');
define('PATH_THEME', PATH_ROOT . '/themes');

// init autoloader
require_once realpath(dirname(__FILE__). '/../../../') . '/src/Bluz/_loader.php';
require_once realpath(dirname(__FILE__). '/../../../') . '/vendor/autoload.php';
require_once 'BootstrapTest.php';
require_once 'TestCase.php';
require_once 'TestListener.php';

// application
$app = Bluz\Tests\BootstrapTest::getInstance();
$app->init('testing');
