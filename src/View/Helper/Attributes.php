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
     * Generate HTML attributes
     *
     * @author The-Who
     *
     * @var View $this
     * @param array $attributes
     * @return string
     */
    function (array $attributes = []) {
        if (empty($attributes)) {
            return '';
        }
        $result = [];
        foreach ($attributes as $key => $value) {
            if (null === $value) {
                // skip empty values
                //  input: ['value'=>null]
                //  output: ''
                continue;
            }
            if (is_int($key)) {
                // allow non-associative keys
                //  input: ['checked']
                //  output: 'checked="checked"'
                $key = $value;
            }
            $result[] = $key . '="' . htmlspecialchars((string)$value, ENT_QUOTES) . '"';
        }

        return join(' ', $result);
    };
