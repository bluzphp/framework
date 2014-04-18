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
     * @param array $link
     * @return string
     */
    function (array $link = null) {
    /** @var View $this */
    if (app()->hasLayout()) {
        // it's stack for <head>
        $layout = app()->getLayout();

        $links = $layout->system('link') ? : [];

        if (null === $link) {
            $links = array_unique($links);
            // prepare to output
            $links = array_map(
                function ($attr) {
                    return '<link '. $this->attributes($attr) .'/>';
                },
                $links
            );
            // clear system vars
            $layout->system('link', []);
            return join("\n", $links);
        } else {
            $links[] = $link;
            $layout->system('link', $links);
        }
    }
    return '';
    };
