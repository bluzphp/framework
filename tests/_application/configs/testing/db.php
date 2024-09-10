<?php

/**
 * Database configuration
 *
 * @link https://github.com/bluzphp/framework/wiki/Db
 * @return array
 */

return [
    'connect' => [
        'type' => 'sqlite',
        'name' => dirname(__DIR__, 3) . DIRECTORY_SEPARATOR .
            '_data' . DIRECTORY_SEPARATOR .
            'framework.sqlite',
    ]
];
