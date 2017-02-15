<?php
/**
 * Database configuration
 *
 * @link https://github.com/bluzphp/framework/wiki/Db
 * @return array
 */
return array(
    "connect" => array(
        "type" => "mysql",
        "host" => "10.10.0.116",
        "name" => "p_bluz_framework_test",
        "user" => "p_bluz",
        "pass" => "bluz_pass",
        "options" => array(
            \PDO::ATTR_PERSISTENT => true,
            \PDO::MYSQL_ATTR_INIT_COMMAND => "SET CHARACTER SET utf8"
        )
    )
);
