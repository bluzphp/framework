<?php
/**
 * Copyright (c) 2013 by Bluz PHP Team
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
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
