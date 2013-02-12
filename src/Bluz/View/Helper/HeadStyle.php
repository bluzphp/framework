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
 * @param string $script
 * @return string|void
 */
function ($style = null, $media = 'all') {
    /** @var View $this */
    if ($this->getApplication()->hasLayout()) {
        // it's stack for <head>
        $view = $this->getApplication()->getLayout();

        $headStyle = $view->system('headStyle') ?: [];

        if (null === $style) {
            // clear system vars
            $view->system('headStyle', []);

            array_walk($headStyle, function(&$item, $key){
                $item = $this->style($key, $item);
            });
            return join("\n", $headStyle);
        } else {
            $headStyle[$style] =  $media;
            $view->system('headStyle', $headStyle);
        }
    } else {
        // it's just alias to script() call
        return $this->style($style);
    }
};