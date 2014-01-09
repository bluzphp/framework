<?php
/**
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
     * @author The-Who
     *
     * @param string $name
     * @param string|null $value
     * @param bool $checked
     * @param array $attributes
     * @return \Closure
     */
    function ($name, $value = null, $checked = false, array $attributes = []) {
    /** @var View $this */
    if (true === $checked) {
        $attributes['checked'] = 'checked';
    }

    if (null !== $value) {
        $attributes['value'] = $value;
    }

    $attributes['name'] = $name;
    $attributes['type'] = 'radio';

    return '<input ' . $this->attributes($attributes) . '/>';
    };
