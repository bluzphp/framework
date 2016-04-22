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

use Bluz\Proxy\Layout;
use Bluz\Proxy\Registry;

return
    /**
     * Switch layout
     *
     * @return void
     */
    function ($layout) {
        Registry::set('app::layout', true);
        Layout::setTemplate($layout);
    };
