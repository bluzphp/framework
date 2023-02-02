<?php

/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */

namespace Bluz\Tests\Fixtures\Controllers;

/**
 * @return void
 * @throws \Exception
 */
return function () {
    $this->disableLayout();
    throw new \Exception('Message', 1024);
};
