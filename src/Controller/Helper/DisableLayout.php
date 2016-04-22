<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Controller\Helper;

use Bluz\Proxy\Registry;

return
    /**
     * Switch layout or disable it
     *
     * @return void
     */
    function () {
        Registry::set('app::layout', false);
    };
