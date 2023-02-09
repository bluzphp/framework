<?php

/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

/**
 * Example of empty test controller
 *
 * @author   Anton Shevchuk
 */

namespace Application;

use Bluz\Controller\Attribute\Method;
use Bluz\Http\RequestMethod;

/**
 * @return bool
 */
return
    #[Method(RequestMethod::POST)]
    function () {
        return false;
    };
