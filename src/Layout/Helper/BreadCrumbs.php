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
namespace Bluz\Layout\Helper;

use Bluz\Layout\Layout;
use Bluz\Proxy\Registry;

return
    /**
     * Set or Get Breadcrumbs
     *
     * @var Layout $this
     * @param array $data
     * @return array|null
     */
    function (array $data = []) {
        if (sizeof($data)) {
            Registry::set('layout:breadcrumbs', $data);
            return null;
        } else {
            return Registry::get('layout:breadcrumbs');
        }
    };
