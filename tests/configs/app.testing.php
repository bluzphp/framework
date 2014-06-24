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
    "logger" => array(
        "enabled" => true,
    ),
    "registry" => array(
        "moo" => "baz"
    ),
    "test" => array(
        "foo" => "bar"
    ),
    "tmp_name" => array(
        "image1" => PATH_ROOT."/tests/src/Fixtures/Http/test.jpg",
        "image2" => PATH_ROOT."/tests/src/Fixtures/Http/test1.jpg"
    )
);
