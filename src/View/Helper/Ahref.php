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
     * @author ErgallM
     *
     * @param string $text
     * @param string|array $href
     * @param array $attributes HTML attributes
     * @return \Closure
     */
    function ($text, $href, array $attributes = []) {
    /** @var View $this */
    // if href is settings for url helper
    if (is_array($href)) {
        $href = call_user_func_array(array($this, 'url'), $href);
    }

    // href can be null, if access is denied
    if (null === $href) {
        return '';
    }

    if ($href == app()->getRequest()->getRequestUri()) {
        if (isset($attributes['class'])) {
            $attributes['class'] .= ' on';
        } else {
            $attributes['class'] = 'on';
        }
    }

    $attributes = $this->attributes($attributes);

    return '<a href="' . $href . '" ' . $attributes . '>' . __($text) . '</a>';
    };
