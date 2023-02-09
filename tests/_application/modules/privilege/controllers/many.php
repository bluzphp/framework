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

use Bluz\Controller\Attribute\Privilege;

/**
 * @return bool
 */
return
    #[Privilege('One')]
    #[Privilege('Two')]
    #[Privilege('Three')]
    function () {
        return false;
    };
