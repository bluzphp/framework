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
     * @param string $title
     * @param string $position
     * @param string $separator
     * return string|View
     */
    function ($title = null, $position = View::POS_REPLACE, $separator = ' :: ') {
    /** @var View $this */
    if (app()->hasLayout()) {
        // it's stack for <head>
        $layout = app()->getLayout();
        if ($title === null) {
            return $layout->system('title');
        } else {
            // switch statement for $position
            switch ($position) {
                case View::POS_PREPEND:
                    $result = $title . (!$layout->system('title') ? : $separator . $layout->system('title'));
                    break;
                case View::POS_APPEND:
                    $result = (!$layout->system('title') ? : $layout->system('title') . $separator) . $title;
                    break;
                case View::POS_REPLACE:
                default:
                    $result = $title;
                    break;
            }
            $layout->system('title', $result);
        }
    }
    return '';
    };
