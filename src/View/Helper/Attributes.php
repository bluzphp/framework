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
 * Generate HTML attributes
 *
 * @author The-Who
 *
 * @var    View  $this
 *
 * @param  array $attributes
 *
 * @return string
 */
return
    function (array $attributes = []) {
        if (empty($attributes)) {
            return '';
        }
        $result = [];
        foreach ($attributes as $key => $value) {
            if (null === $value) {
                // skip empty values
                //   input: [attribute=>null]
                //   output: ''
                continue;
            } elseif (is_int($key)) {
                // allow non-associative keys
                //   input: [checked, disabled]
                //   output: 'checked disabled'
                $result[] = $value;
            } else {
                $result[] = $key . '="' . htmlspecialchars((string)$value, ENT_QUOTES) . '"';
            }
        }

        return implode(' ', $result);
    };
