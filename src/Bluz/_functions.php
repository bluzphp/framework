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
 * Simple functions of framework
 * be careful with this way
 *
 * @author   Anton Shevchuk
 * @created  07.09.12 11:29
 */
if (!function_exists('debug')) {
    /**
     * Debug variables with \Bluz\Debug::dump
     *
     * @return void
     */
    function debug() {
        // check definition
        if (!defined('DEBUG') or !DEBUG) {
            return;
        }

        //
        // try to get caller information
        //
        $backtrace = debug_backtrace();

        // get variable name
        $arrLines = file($backtrace[0]["file"]);
        $code = $arrLines[($backtrace[0]["line"] - 1)];
        $arrMatches = array();

        // find call to debug() by regexp
        preg_match('/\b\s*debug\s*\(\s*(.+)\s*\);\s*/i', $code, $arrMatches);

        $varName = isset($arrMatches[1]) ? $arrMatches[1] : '???';

        $where = basename($backtrace[0]["file"]) . ':' . $backtrace[0]["line"];

        echo "<h2>Debug: {$varName} ({$where})</h2>";

        //
        // call \Bluz\Debug::dump()
        //
        call_user_func_array(['\Bluz\Debug', 'dump'], func_get_args());
    }
}