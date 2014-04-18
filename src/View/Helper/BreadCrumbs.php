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
     * @param array $data
     * @return array|null
     */
    function (array $data = []) {
    /** @var View $this */
    if (app()->hasLayout()) {
        $layout = app()->getLayout();
        if (sizeof($data)) {
            $layout->system('breadcrumbs', $data);
        } else {
            return $layout->system('breadcrumbs');
        }
    }
    return null;
    };
