<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

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
    protected $startTime;

    /**
     * @var float part time
     */
    protected $timer;

    /**
     * @var integer
     */
    protected $memory = 0;

    /**
     * @var array list of alerts
     */
    protected $alert = [];

    /**
     * @var array list of critical
     */
    protected $critical = [];

    /**
     * @var array list of debug messages
     */
    protected $debug = [];

    /**
     * @var array list of emergency
     */
    protected $emergency = [];

    /**
     * @var array list of errors
     */
    protected $error = [];

    /**
     * @var array list of info
     */
    protected $info = [];

    /**
     * @var array list of notices
     */
    protected $notice = [];

    /**
     * @var array list of warnings
     */
    protected $warning = [];

    /**
     * Interpolates context values into the message placeholders
     *
     * @param  string $message
     * @param  array  $context
     *
     * @return string
     */
    protected function interpolate($message, array $context = []): string
    {
        // build a replacement array with braces around the context keys
        $replace = [];
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
     *
     * @return void
     */
    public function info($message, array $context = [])
    {
        $message = $this->interpolate($message, $context);

        if (!$this->startTime) {
            $this->startTime = $this->timer = $_SERVER['REQUEST_TIME_FLOAT'] ?? microtime(true);
        }

        $curTimer = microtime(true);
        $curMemory = memory_get_usage();

        $key = sprintf(
            '%f :: %f :: %d',
            $curTimer - $this->startTime,
            $curTimer - $this->timer,
            $curMemory - $this->memory
        );

        $this->info[$key] = $message;

        $this->timer = $curTimer;
        $this->memory = $curMemory;
    }

    /**
     * Logs with an arbitrary level
     *
     * @param  mixed  $level
     * @param  string $message
     * @param  array  $context
     *
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
     *
     * @return array
     */
    public function get($level)
    {
        return $this->{$level};
    }
}
