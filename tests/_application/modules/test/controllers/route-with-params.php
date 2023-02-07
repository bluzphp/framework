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

use Bluz\Controller\Attribute\Route;

/**
 * @return bool
 */
return
    #[Route('/{$a}-{$b}-{$c}/')]
    function (int $a, float $b, string $c) {
        return false;
    };
