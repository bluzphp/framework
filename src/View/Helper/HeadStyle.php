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
     * @param string $script
     * @return string|void
     */
    function ($style = null, $media = 'all') {
    /** @var View $this */
    if (app()->hasLayout()) {
        // it's stack for <head>
        $view = app()->getLayout();

        $headStyle = $view->system('headStyle') ? : [];

        if (null === $style) {
            // clear system vars
            $view->system('headStyle', []);

            array_walk(
                $headStyle,
                function (&$item, $key) {
                    $item = $this->style($key, $item);
                }
            );
            return join("\n", $headStyle);
        } else {
            $headStyle[$style] = $media;
            $view->system('headStyle', $headStyle);
        }
    } else {
        // it's just alias to script() call
        return $this->style($style);
    }
    };
