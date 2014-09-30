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
     * Generate HTML for <input type="checkbox">
     *
     * @author The-Who
     *
     * @var View $this
     * @param string $name
     * @param string|null $value
     * @param bool $checked
     * @param array $attributes
     * @return string
     */
    function ($name, $value = null, $checked = false, array $attributes = []) {
        /** @var View $this */
        if (true === $checked) {
            $attributes['checked'] = 'checked';
        } elseif (false !== $checked && ($checked == $value)) {
            $attributes['checked'] = 'checked';
        }

        if (null !== $value) {
            $attributes['value'] = $value;
        }

        $attributes['name'] = $name;
        $attributes['type'] = 'checkbox';

        return '<input ' . $this->attributes($attributes) . '/>';
    };
