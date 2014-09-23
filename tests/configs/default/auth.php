<?php
/**
 * Auth configuration
 *
 * @link https://github.com/bluzphp/framework/wiki/Auth
 * @return array
 */
return array(
    "equals" => array(
        "encryptFunction" => function ($password, $salt) {
            return md5(md5($password) . $salt);
        }
    ),
    "facebook" => array(
        "appId" => "%%appId%%",
        "secret" => "%%secret%%",
    ),
    "twitter" => array(
        "consumerKey" => "%%consumerKey%%",
        "consumerSecret" => "%%consumerSecret%%"
    )
);
