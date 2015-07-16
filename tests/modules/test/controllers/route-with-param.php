<?php
/**
 * Example of route with one param
 *
 * @author   Anton Shevchuk
 * @created  12.06.12 13:08
 */
namespace Application;

return
/**
 * @route /test/param/$
 * @route /test/param/{$a}/
 * @param string $a
 * @return \closure
 */
function ($a = 42) {
    return false;
};
