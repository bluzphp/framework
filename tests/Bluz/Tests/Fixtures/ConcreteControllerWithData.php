<?php
/**
 * 
 *
 * @category Application
 *
 * @author   dark
 * @created  21.05.13 10:46
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
function($a, $b, $c = null){
    /**
     * @var \Bluz\Application $this
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