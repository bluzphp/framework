<?php

/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

/**
 * Example route with params
 *
 * @category Application
 *
 * @author   dark
 * @created  18.12.13 18:39
 */

namespace Application;

use Bluz\Controller\Attribute\Route;

/**
 * @return bool
 */
return
    #[Route('/test/route-with-other-params/{$alias}(.*)')]
    function (string $alias) {
        return false;
    };
