<?php
/**
 * Copyright (c) 2012 by Bluz PHP Team
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
 * @author ErgallM
 *
 * @param string $text
 * @param string|array $href
 * @param array $attributes HTML attributes
 * @return \Closure
 */
function ($text, $href, array $attributes = []) {
    // if href is settings for url helper
    if (is_array($href)) {
        $href = call_user_func_array(array($this, 'url'), $href);
    }

    // href can be null, if access is denied
    if (null === $href) return '';

    if ($href == $this->getApplication()->getRequest()->getRequestUri()) {
        if (isset($attributes['class'])) {
            $attributes['class'] .= ' on';
        } else {
            $attributes['class'] = 'on';
        }
    }
    $attrs = [];

    foreach ($attributes as $attr => $value) {
        $attrs[] = $attr . '="' . $value . '"';
    }

    return '<a href="' . $href . '" ' . join(' ', $attrs) . '>' . $this->__($text) . '</a>';
};