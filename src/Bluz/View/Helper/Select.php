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
 * @author The-Who
 *
 * @param string $name
 * @param array $options
 * @param array|string $selected
 * @param array $attributes
 * @return \Closure
 */
function ($name, array $options = [], $selected = null, array $attributes = []) {
    /** @var View $this */
    if (is_array($selected) && count($selected) > 1) {
        $attributes['multiple'] = 'multiple';
    }

    if (!is_array($selected) && null !== $selected) {
        $selected = array($selected);
    }

    $attributes['name'] = $name;

    if (null === $selected) {
        $selected = array();
    }
    $opts = [];

    foreach ($options as $value => $text) {
        $opts[] = '<option value="' . $value . '" ' .
                ((in_array($value, $selected)) ? 'selected' : '') . '>' . $text . '</option>';
    }

    return '<select '. join(' ', $this->attributes($attributes)) .'>' . join($opts) . '</select>';
};