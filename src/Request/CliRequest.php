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
namespace Bluz\Request;

use Bluz\Request;

/**
 * Request
 *
 * @category Bluz
 * @package  Request
 *
 * @author   Anton Shevchuk
 * @created  06.07.11 16:59
 */
class CliRequest extends AbstractRequest
{
    /**
     * Constructor
     *
     * @example $> php index.php controller module --param value
     */
    public function __construct()
    {
        parent::__construct();
        $this->method = 'CLI';

        $args = $_SERVER["argv"];
        unset($args['0']);

        $searchActions =  true;
        $actions = array();

        foreach ($args as $arg) {
            if (strpos($arg, '-') === 0) {
                $searchActions = false;
                $this->params[trim($arg, '-')] = null;
            } elseif ($searchActions) {
                $actions[] = $arg;
            } else {
                if (count($this->params)) {
                    end($this->params);

                    $optionName = key($this->params);
                    if (!empty($this->params[ $optionName ])) {
                        $this->params[ $optionName ] = (array) $this->params[ $optionName ];
                        $this->params[ $optionName ][] = $arg;
                    } else {
                        $this->params[ $optionName ] = $arg;
                    }
                }
            }
        }

        $this->setRequestUri(join('/', $actions));
    }

    /**
     * Get the request URI.
     *
     * @return string
     */
    public function getRequestUri()
    {
        if ($this->requestUri === null) {
            $this->setRequestUri('');
        }
        return $this->requestUri;
    }


    /**
     * Get the base URL.
     *
     * @return string
     */
    public function getBaseUrl()
    {
        if (null === $this->baseUrl) {
            $this->setBaseUrl('');
        }
        return $this->baseUrl;
    }

    /**
     * Get the client's IP address
     *
     * @param  boolean $checkProxy
     * @return string
     */
    public function getClientIp($checkProxy = true)
    {
        return '127.0.0.1';
    }
}
