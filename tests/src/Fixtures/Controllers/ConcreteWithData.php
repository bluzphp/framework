<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Tests\Fixtures\Controllers;

return
/**
 * @privilege Test
 * @cache 5 min
 * @cache-html 1 min
 * @method CLI
 * @method GET
 * @route /example/
 * @route /example/{$a}/{$b}/{$c}
 * @param int $a
 * @param float $b
 * @param string $c
 * @return array
 */
function ($a, $b, $c = null) {
    /**
     * @var \Bluz\Application\Application $this
     */
    return array();
};
