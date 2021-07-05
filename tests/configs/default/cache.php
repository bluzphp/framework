<?php

/**
 * Cache configuration
 *
 * @link https://github.com/bluzphp/framework/wiki/Cache
 * @return array
 */

return [
    'enabled' => false,
    'adapter' => 'memcached',
    'pools' => [
        /**
         * @link https://symfony.com/doc/current/components/cache/adapters/apcu_adapter.html
         */
        'apcu' => function () {
            return new Symfony\Component\Cache\Adapter\ApcuAdapter('bluz');
        },
        /**
         * @link https://symfony.com/doc/current/components/cache/adapters/filesystem_adapter.html
         */
        'filesystem' => function () {
            return new Symfony\Component\Cache\Adapter\FilesystemTagAwareAdapter('bluz', 0, PATH_DATA . '/cache');
        },
        /**
         * @link https://symfony.com/doc/current/components/cache/adapters/redis_adapter.html
         * @link https://github.com/nrk/predis/wiki/Connection-Parameters
         * @link https://github.com/nrk/predis/wiki/Client-Options
         */
        'predis' => function () {
            $client = new \Predis\Client('tcp:/127.0.0.1:6379');
            return new Symfony\Component\Cache\Adapter\RedisTagAwareAdapter($client, 'bluz');
        }
    ]
];
