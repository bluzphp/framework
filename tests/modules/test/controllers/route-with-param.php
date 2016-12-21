<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * Example of route with one param
 *
 * @author   Anton Shevchuk
 * @created  12.06.12 13:08
 */
namespace Application;

/**
 * @route /test/param/$
 * @route /test/param/{$a}/
 * @param integer $a
 * @return bool
 */
return function ($a = 42) {
    return false;
};
