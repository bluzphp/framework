<?php
/**
 * Application config
 *
 * @author   Anton Shevchuk
 * @created  08.07.11 12:14
 */
return array(
    "cache" => array(
        "enabled" => false
    ),
    "db" => array(
        "connect" => array(
            "type" => "mysql",
            "host" => "localhost",
            "name" => "bluz",
            "user" => "root",
            "pass" => "",
        ),
    ),
    "profiler" => false,
);
