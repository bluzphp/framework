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
     * @param string $style
     * @param string $media
     * @return string|View
     */
    function ($style, $media = 'all') {
    /** @var View $this */
    if ('.css' == substr($style, -4)) {
        if (strpos($style, 'http://') !== 0
            && strpos($style, 'https://') !== 0
        ) {
            $style = $this->baseUrl($style);
        }
        return "\t<link href=\"" . $style . "\" rel=\"stylesheet\" media=\"" . $media . "\"/>\n";
    } else {
        return "\t<style type=\"text/css\" media=\"" . $media . "\">\n"
        . $style . "\n"
        . "\t</style>";
    }
    };
