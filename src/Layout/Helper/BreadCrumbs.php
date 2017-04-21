<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Layout\Helper;

use Bluz\Proxy\Registry;

/**
 * Set or Get Breadcrumbs
 *
 * @param  array $data
 * @return array|null
 */
return
    function (array $data = []) {
        if (count($data)) {
            Registry::set('layout:breadcrumbs', $data);
            return null;
        } else {
            return Registry::get('layout:breadcrumbs');
        }
    };
