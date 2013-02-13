<?php
// Environment
define('DEBUG', true);

// init autoloader
require_once realpath(dirname(__FILE__). '/../../../') . '/vendor/autoload.php';
require_once 'BootstrapTest.php';
require_once 'TestCase.php';
require_once 'TestListener.php';