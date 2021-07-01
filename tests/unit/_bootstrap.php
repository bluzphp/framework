<?php

// Here you can initialize variables that will be available to your tests
// Emulate session
$_SESSION = [];
$_COOKIE[session_name()] = uniqid('bluz-framework-test', false);
