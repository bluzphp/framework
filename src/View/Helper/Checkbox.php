<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\View\Helper;

use Bluz\View\View;

/**
 * Generate HTML for <input type="checkbox">
 *
 * @author The-Who
 *
 * @param  string      $name
 * @param  string|null $value
 * @param  bool        $checked
 * @param  array       $attributes
 *
 * @return string
 */
return
    function ($name, $value = null, $checked = false, array $attributes = []) {
        /** @var View $this */
        if (true === $checked) {
            $attributes['checked'] = 'checked';
        } elseif (false !== $checked && ($checked === $value)) {
            $attributes['checked'] = 'checked';
        }

        if (null !== $value) {
            $attributes['value'] = $value;
        }

        $attributes['name'] = $name;
        $attributes['type'] = 'checkbox';

        return '<input ' . $this->attributes($attributes) . '/>';
    };
