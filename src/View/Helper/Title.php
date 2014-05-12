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
     * Set or generate <title> code for <head>
     *
     * @var View $this
     * @param string $title
     * @param string $position
     * @param string $separator
     * @return string|View
     */
    function ($title = null, $position = View::POS_REPLACE, $separator = ' :: ') {

    if (app()->hasLayout()) {
        // it's stack for <title> tag
        if (null === $title) {
            return app()->getRegistry()->__get('layout:title');
        } else {
            $oldTitle = app()->getRegistry()->__get('layout:title');
            // switch statement for $position
            switch ($position) {
                case View::POS_PREPEND:
                    $result = $title . (!$oldTitle ? : $separator . $oldTitle);
                    break;
                case View::POS_APPEND:
                    $result = (!$oldTitle ? : $oldTitle . $separator) . $title;
                    break;
                case View::POS_REPLACE:
                default:
                    $result = $title;
                    break;
            }
            app()->getRegistry()->__set('layout:title', $result);
            return $this;
        }
    }
    return '';
    };
