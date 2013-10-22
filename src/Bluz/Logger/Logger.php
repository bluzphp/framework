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
namespace Bluz\Logger;

use Bluz\Common\Package;
use Psr\Log\AbstractLogger;

/**
 * Logger
 *
 * @category Bluz
 * @package  Logger
 *
 * @author   Taras Omelianenko <mail@taras.pro>
 */
class Logger extends AbstractLogger
{
    use Package;

    /**
     * @var null
     */
    protected $start = null;
    protected $timer = null;

    /**
     * @var array
     */
    protected $alert = array();
    protected $critical = array();
    protected $debug = array();
    protected $emergency = array();
    protected $error = array();
    protected $info = array();
    protected $notice = array();
    protected $warning = array();

    /**
     * Interpolates context values into the message placeholders
     *
     * @param string $message
     * @param array $context
     * @return string
     */
    protected function interpolate($message, array $context = [])
    {
        // build a replacement array with braces around the context keys
        $replace = array();
        foreach ($context as $key => $val) {
            $replace['{' . $key . '}'] = $val;
        }

        // interpolate replacement values into the message and return
        return strtr($message, $replace);
    }

    /**
     * log
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function info($message, array $context = [])
    {
        $message = $this->interpolate($message, $context);

        if (!$this->start) {
            $this->start = $this->timer = isset($_SERVER['REQUEST_TIME_FLOAT'])
                ? $_SERVER['REQUEST_TIME_FLOAT']
                : microtime(true);
        }

        $curTimer = microtime(true);
        $curMemory = ceil((memory_get_usage() / 1024));

        $key = sprintf(
            "%f :: %f :: %s kb",
            ($curTimer - $this->start),
            ($curTimer - $this->timer),
            $curMemory
        );

        $this->info[$key] = $message;

        $this->timer = $curTimer;
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return void
     */
    public function log($level, $message, array $context = [])
    {
        $this->{$level}[] = $this->interpolate($message, $context);
    }

    /**
     * get
     *
     * @param $level
     * @return array
     */
    public function get($level)
    {
        return $this->{$level};
    }
}
