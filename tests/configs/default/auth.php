<?php
/**
 * Auth configuration
 *
 * @link https://github.com/bluzphp/framework/wiki/Auth
 * @return array
 */
return [
    'equals' => [
        'encryptFunction' => function ($password, $salt) {
            return md5(md5($password) . $salt);
        }
    ],
    'facebook' => [
        'appId' => '%%appId%%',
        'secret' => '%%secret%%',
    ],
    'twitter' => [
        'consumerKey' => '%%consumerKey%%',
        'consumerSecret' => '%%consumerSecret%%'
    ]
];
