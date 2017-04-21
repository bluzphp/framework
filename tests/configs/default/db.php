<?php
/**
 * Database configuration
 *
 * @link https://github.com/bluzphp/framework/wiki/Db
 * @return array
 */
return [
    'connect' => [
        'type' => 'mysql',
        'host' => 'localhost',
        'name' => 'bluz',
        'user' => 'root',
        'pass' => '',
        'options' => [
            \PDO::ATTR_PERSISTENT => true,
            \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET CHARACTER SET utf8'
        ]
    ]
];
