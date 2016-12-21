<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Tests\Fixtures\Controllers;

/**
 * @throws \Exception
 * @return void
 */
return function () {
    $this->disableLayout();
    throw new \Exception('Message', 1024);
};
