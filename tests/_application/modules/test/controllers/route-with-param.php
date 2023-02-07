<?php

/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

/**
 * Example of route with one param
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
    #[Route('/test/param/$')]
    #[Route('/test/param/{$a}/')]
    function (int $a = 42) {
        return false;
    };
