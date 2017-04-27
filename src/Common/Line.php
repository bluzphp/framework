<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Common;

/**
 * Line is string :)
 *
 * @package  Bluz\Common
 * @author   Anton Shevchuk
 */
class Line
{
    /**
     * Convert string to camel case
     *
     * @param  string $subject
     * @return string
     */
    public static function toCamelCase($subject) : string
    {
        $subject = str_replace(['_', '-'], ' ', strtolower($subject));
        return str_replace(' ', '', ucwords($subject));
    }
}
