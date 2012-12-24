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
 * Setup redactorjs
 *
 * @param string $selector
 * @param array|string|null $settings
 * @return string
 */
function ($selector, $settings = null) {
    /** @var View $this */
    if ($settings === null) {
        $settings = "{}";
    } elseif (is_array($settings)) {
        $settings = json_encode($settings);
    } elseif (!is_string($settings)) {
        $settings = "{}";
    }


    $html  = "";
    $html .= $this->headScript('redactor/redactor.js');
    $html .= $this->headStyle('redactor/redactor.css');
    $html .= $this->script('require(["jquery", "bluz"], function($, bluz) {
        bluz.ready(function(){
            $("'.$selector.'").redactor('.$settings.');
        });
    });');
    return $html;
};