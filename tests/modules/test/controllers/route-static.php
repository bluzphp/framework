<?php
/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

/**
 * Example of static route
 *
 * @author   Anton Shevchuk
 * @created  12.06.12 13:08
 */

namespace Application;

/**
 * @route /static-route/
 * @route /another-route.html
 * @return bool
 */
return function () {
    return false;
};
