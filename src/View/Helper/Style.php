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
 * Generate HTML for <style> or <link> element
 *
 * @param  string $style
 * @param  string $media
 * @return string
 */
return
    function ($style, $media = 'all') {
        /**
         * @var View $this
         */
        if ('.css' == substr($style, -4)) {
            if (strpos($style, 'http://') !== 0
                && strpos($style, 'https://') !== 0
            ) {
                $style = $this->baseUrl($style);
            }
            return "<link href=\"$style\" rel=\"stylesheet\" media=\"$media\"/>\n";
        } else {
            return "<style type=\"text/css\" media=\"$media\">\n$style\n</style>\n";
        }
    };
