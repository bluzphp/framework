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
namespace Bluz\View\Helper;

use Bluz\View\View;
use Bluz\Proxy\Registry;

return
    /**
     * Set or Get Breadcrumbs
     *
     * @var View $this
     * @param array $data
     * @return array|View
     */
    function (array $data = []) {
    if (app()->hasLayout()) {
        if (sizeof($data)) {
            Registry::set('layout:breadcrumbs', $data);
            return $this;
        } else {
            return Registry::get('layout:breadcrumbs');
        }
    }
    return [];
    };
