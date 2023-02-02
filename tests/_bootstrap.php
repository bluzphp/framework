<?php

// This is global bootstrap for autoloading
// Environment
define('DEBUG', true);
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Paths
define('PATH_ROOT', realpath(dirname(__DIR__)));
define('PATH_APPLICATION', __DIR__ . DIRECTORY_SEPARATOR . '_application');
define('PATH_DATA', __DIR__ . DIRECTORY_SEPARATOR . '_data');
define('PATH_VENDOR', PATH_ROOT . DIRECTORY_SEPARATOR . 'vendor');
