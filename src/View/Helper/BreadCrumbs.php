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
            app()->getRegistry()->__set('layout:breadcrumbs', $data);
            return $this;
        } else {
            return app()->getRegistry()->__get('layout:breadcrumbs');
        }
    }
    return [];
    };
