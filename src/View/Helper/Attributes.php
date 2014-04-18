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
     * @author The-Who
     *
     * @param array $attributes
     * @return \Closure
     */
    function (array $attributes = []) {
    /** @var View $this */
    if (empty($attributes)) {
        return '';
    }
    $result = [];
    foreach ($attributes as $key => $value) {
        if (null === $value) {
            // skip null values
            // ['value'=>null] => ''
            continue;
        }
        if (is_int($key)) {
            // allow non-associative keys
            // ['checked'] => 'checked="checked"'
            $key = $value;
        }
        $result[] = $key . '="' . htmlspecialchars((string)$value, ENT_QUOTES) . '"';
    }

    return join(' ', $result);
    };
