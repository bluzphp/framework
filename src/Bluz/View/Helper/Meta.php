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

/**
 * @param string|array|null $name
 * @param string|null $content
 * @return string|View
 */
return function ($name = null, $content = null) {
    /** @var View $this */
    if (!$meta = $this->system('meta')) {
        $meta = array();
    }

    if ($name && $content) {
        $meta[] = ['name' => $name, 'content' => $content];
    } elseif (is_array($name)) {
        $meta[] = $name;
    } elseif (!$name && !$content) {
        if (sizeof($meta)) {
            // prepare to output
            $meta = array_map(function($arr){
                $str = '<meta ';
                foreach ($arr as $key => $value) {
                    $str .= $key .'="'. addcslashes($value, '"') .'" ';
                }
                $str .= '/>';
                return $str;
            }, $meta);
            // clear system vars
            $this->system('meta', []);
            return join("\n", $meta);
        } else {
            return '';
        }
    }

    $this->system('meta', $meta);
    return $this;
};