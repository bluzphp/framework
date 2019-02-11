<?php
/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */

namespace Bluz\Tests\Fixtures\Controllers;

use Bluz\Proxy\Response;

/**
 * @return void
 */
return function () {
    Response::redirectTo('index');
};
