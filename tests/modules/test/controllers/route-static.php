<?php
/**
 * Example of static route
 *
 * @author   Anton Shevchuk
 * @created  12.06.12 13:08
 */
namespace Application;

use Bluz;

return
/**
 * @route /static-route/
 * @route /another-route.html
 * @return \closure
 */
function () {
    return false;
};
