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
namespace Bluz\Layout\Helper;

use Bluz\Layout\Layout;
use Bluz\Proxy\Registry;

return
    /**
     * Set or generate <title> code for <head>
     *
     * @var Layout $this
     * @param string $title
     * @param string $position
     * @param string $separator
     * @return string|null
     */
    function ($title = null, $position = Layout::POS_REPLACE, $separator = ' :: ') {
        // it's stack for <title> tag
        if (null === $title) {
            return Registry::get('layout:title');
        } else {
            $oldTitle = Registry::get('layout:title');
            // switch statement for $position
            switch ($position) {
                case Layout::POS_PREPEND:
                    $result = $title . (!$oldTitle ? : $separator . $oldTitle);
                    break;
                case Layout::POS_APPEND:
                    $result = (!$oldTitle ? : $oldTitle . $separator) . $title;
                    break;
                case Layout::POS_REPLACE:
                default:
                    $result = $title;
                    break;
            }
            Registry::set('layout:title', $result);
            return null;
        }
    };
