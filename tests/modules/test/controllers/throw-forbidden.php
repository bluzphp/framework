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
 * @throws ForbiddenException
 * @return void
 */
return function () {
    $this->disableLayout();
    throw new ForbiddenException('Forbidden');
};
