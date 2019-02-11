<?php
// This is global bootstrap for autoloading
// Environment
define('DEBUG', true);
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Paths
define('PATH_ROOT', realpath(dirname(__DIR__)));
define('PATH_APPLICATION', PATH_ROOT . DIRECTORY_SEPARATOR . 'tests');
define('PATH_VENDOR', PATH_ROOT . DIRECTORY_SEPARATOR . 'vendor');

// Use composer autoload
$loader = require PATH_ROOT . '/vendor/autoload.php';
$loader->addPsr4('Bluz\\Tests\\', __DIR__ . DIRECTORY_SEPARATOR . 'src');
