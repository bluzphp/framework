<?php
// Environment
define('DEBUG', true);
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 1);

// Paths
define('PATH_ROOT', realpath(dirname(__FILE__). '/../'));
define('PATH_APPLICATION', PATH_ROOT . '/tests');
define('PATH_VENDOR', PATH_ROOT . '/vendor');

// Emulate session
$_SESSION = array();
$_COOKIE[session_name()] = uniqid();

// init autoloader
require_once PATH_ROOT . '/vendor/autoload.php';

// init application
$env = getenv('BLUZ_ENV') ?: 'testing';

$app = \Bluz\Tests\BootstrapTest::getInstance();
$app->init($env);
