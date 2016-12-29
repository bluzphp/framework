<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\View\Helper;

use Bluz\View\View;

/**
 * Generate HTML for <script> element
 *
 * @param  string $src
 * @param  array  $attributes HTML attributes
 * @return string
 */
return
    function ($src, array $attributes = []) {
        /**
         * @var View $this
         */
        if (strpos($src, 'http://') !== 0
            && strpos($src, 'https://') !== 0
            && strpos($src, '//') !== 0
        ) {
            $src = $this->baseUrl($src);
        }
        $attributes = $this->attributes($attributes);
        return "<script src=\"$src\" $attributes></script>\n";
    };
