<?php

/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

/**
 * Example of route with params
 *
 * @author   Anton Shevchuk
 * @created  12.06.12 13:08
 */

namespace Application;

/**
 * @route /{$a}-{$b}-{$c}/
 *
 * @param int    $a
 * @param float  $b
 * @param string $c
 *
 * @return bool
 */
return function ($a, $b, $c) {
    return false;
};
