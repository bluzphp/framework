<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Controller\Helper;

use Bluz\Http\Exception\NotAllowedException;
use Bluz\Controller\Controller;
use Bluz\Proxy\Request;

/**
 * Denied helper can be declared inside Bootstrap
 *
 * @return void
 */
return
    function () {
        /**
         * @var Controller $this
         */
        $methods = $this->getMeta()->getMethod();
        if ($methods && !in_array(Request::getMethod(), $methods, true)) {
            throw new NotAllowedException(implode(',', $methods));
        }
    };
