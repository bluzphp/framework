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

use Bluz\Controller\Attribute\Route;

/**
 * @return bool
 */
return
    #[Route('/static-route/')]
    #[Route('/another-route.html')]
    function () {
        return false;
    };
