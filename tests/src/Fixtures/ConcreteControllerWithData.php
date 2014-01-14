<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Application;

return
/**
 *
 * @privilege Test
 * @method CLI
 * @method GET
 * @route /example/
 * @route /example/{$a}/{$b}/{$c}
 * @param int $a
 * @param float $b
 * @param string $c
 * @return \closure
 */
function ($a, $b, $c = null) {
    /**
     * @var \Bluz\Application\Application $this
     */
    return array(
        'params' => array("a" => "int", "b" => "float", "c" => "string"),
        'values' => array("c" => null),
        'route'  => array(
            '/example/',
            '/example/{$a}/{$b}/{$c}'
        ),
        'privilege' => "Test",
        'method' => array("CLI", "GET"),
//        'return' => '\closure',
    );
};
