<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Common;

/**
 * Instance
 *
 * @package  Bluz\Common
 * @author   Anton Shevchuk
 */
trait Instance
{
    /**
     * Get instance
     * @return static
     */
    public static function getInstance()
    {
        static $instance;
        if (null === $instance) {
            $instance = new static();
        }
        return $instance;
    }
}
