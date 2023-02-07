<?php

/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */

namespace Bluz\Tests\Fixtures\Controllers;

use Bluz\Http\Exception\RedirectException;
use Bluz\Proxy\Response;

/**
 * @return void
 * @throws RedirectException
 */
return function () {
    Response::redirectTo('index');
};
