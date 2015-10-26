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
namespace Bluz\Logger;

use Bluz\Common\Options;
use Psr\Log\AbstractLogger;

/**
 * Logger
 *
 * @package  Bluz\Logger
 * @author   Taras Omelianenko <mail@taras.pro>
 * @link     https://github.com/bluzphp/framework/wiki/Logger
 */
class Logger extends AbstractLogger
{
    use Options;

    /**
     * @var float start time
     */
    protected $start;

    /**
     * @var float part time
     */
    protected $timer;

    /**
     * @var array list of alerts
     */
    protected $alert = array();

    /**
     * @var array list of critical
     */
    protected $critical = array();

    /**
     * @var array list of debug messages
     */
    protected $debug = array();

    /**
     * @var array list of emergency
     */
    protected $emergency = array();

    /**
     * @var array list of errors
     */
    protected $error = array();

    /**
     * @var array list of info
     */
    protected $info = array();

    /**
     * @var array list of notices
     */
    protected $notice = array();

    /**
     * @var array list of warnings
     */
    protected $warning = array();

    /**
     * Interpolates context values into the message placeholders
     *
     * @param  string $message
     * @param  array  $context
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
     * Log info message
     *
     * @param  string $message
     * @param  array  $context
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
     * Logs with an arbitrary level
     *
     * @param  mixed  $level
     * @param  string $message
     * @param  array  $context
     * @return void
     */
    public function log($level, $message, array $context = [])
    {
        $this->{$level}[] = $this->interpolate($message, $context);
    }

    /**
     * Get logs records by level
     *
     * @param  $level
     * @return array
     */
    public function get($level)
    {
        return $this->{$level};
    }
}
