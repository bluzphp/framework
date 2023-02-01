<?php

/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */

namespace Bluz\Tests\Fixtures\Controllers;

use Bluz\Http\Exception\ForbiddenException;

/**
 * @return void
 * @throws ForbiddenException
 */
return function () {
    $this->disableLayout();
    throw new ForbiddenException('Forbidden');
};
