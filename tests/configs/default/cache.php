<?php
/**
 * Cache configuration
 *
 * @link https://github.com/bluzphp/framework/wiki/Cache
 * @return array
 */
return array(
    "enabled" => false,
    "adapter" => "memcached",
    "prefix" => "bluz:",
    "tagAdapter" => "memcached",
    "tagPrefix" => "bluz:@:",
    "settings" => array(
        "apc" => array(),
        /**
         * @link http://php.net/manual/en/memcached.addservers.php
         * @link http://php.net/manual/en/memcached.setoptions.php
         */
        "memcached" => array(
            "persistent" => "uid",
            "servers" => [
                ["localhost", 11211, 1],
            ],
            "options" => array()
        ),
        "phpFile" => array(
            "cacheDir" => PATH_ROOT . '/tests/cache'
        ),
        /**
         * @link https://github.com/nicolasff/phpredis#connection
         * @link https://github.com/nicolasff/phpredis#setoption
         */
        "redis" => array(
            "host" => 'localhost',
            "options" => array()
        ),
        /**
         * @link https://github.com/nrk/predis/wiki/Connection-Parameters
         * @link https://github.com/nrk/predis/wiki/Client-Options
         */
        "predis" => array(
            "host" => 'localhost',
            "options" => array()
        )
    )
);
