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
use Bluz\Proxy\Registry;

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
                return Registry::get('layout:title');
            } else {
                $oldTitle = Registry::get('layout:title');
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
                Registry::set('layout:title', $result);
                return $this;
            }
        }
        return '';
    };
