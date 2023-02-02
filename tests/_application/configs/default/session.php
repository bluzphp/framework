<?php

/**
 * Session configuration
 *
 * @link https://github.com/bluzphp/framework/wiki/Session
 * @return array
 */

return [
    'adapter' => 'files',
    'settings' => [
        'cache' => [],
        'files' => [
            'save_path' => PATH_DATA . '/sessions'
        ],
        'redis' => [
            'host' => 'localhost'
        ]
    ]
];
