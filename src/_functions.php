<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

use Bluz\Common\Collection;
use Bluz\Common\Str;
use Bluz\Translator\Translator;

/**
 * Simple functions of framework
 * be careful with this way
 *
 * @author   Anton Shevchuk
 */
if (!function_exists('debug')) {
    /**
     * Debug variables
     *
     * Example of usage
     *     debug(123);
     *     debug(new stdClass());
     *     debug($_GET, $_POST, $_FILES);
     *
     * @codeCoverageIgnore
     *
     * @param mixed ...$params
     */
    function debug(...$params)
    {
        // check definition
        if (!getenv('BLUZ_DEBUG') || !isset($_COOKIE['BLUZ_DEBUG'])) {
            return;
        }

        ini_set('xdebug.var_display_max_children', '512');

        if (PHP_SAPI === 'cli') {
            if (extension_loaded('xdebug')) {
                // try to enable CLI colors
                ini_set('xdebug.cli_color', '1');
                xdebug_print_function_stack();
            } else {
                debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
            }
            var_dump($params);
        } else {
            echo '<div class="textleft clear"><pre class="bluz-debug">';
            debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
            var_dump($params);
            echo '</pre></div>';
        }
    }
}

if (!function_exists('esc')) {
    /**
     * Escape variable for use in View
     *
     * Example of usage
     *     esc($_GET['name']);
     *     esc($_GET['name'], ENT_QUOTES);
     *
     * @param  string  $variable
     * @param  integer $flags
     *
     * @return string
     */
    function esc($variable, int $flags = ENT_HTML5)
    {
        return htmlentities((string)$variable, $flags, 'UTF-8');
    }
}

if (!function_exists('str_trim_end')) {
    /**
     * Added symbol to end of string, trim it before
     *
     * @param  string $subject
     * @param  string $symbols
     *
     * @return string
     */
    function str_trim_end($subject, $symbols)
    {
        return rtrim($subject, $symbols) . $symbols;
    }
}

if (!function_exists('to_camel_case')) {
    /**
     * Convert string to camel case
     *
     * @param  string $subject
     *
     * @return string
     */
    function to_camel_case($subject)
    {
        return Str::toCamelCase($subject);
    }
}

if (!function_exists('class_namespace')) {
    /**
     * Get namespace of class
     *
     * @param  string $class
     *
     * @return string
     */
    function class_namespace($class)
    {
        return substr($class, 0, strrpos($class, '\\'));
    }
}

if (!function_exists('value')) {
    /**
     * Return the value for callable
     *
     * @param  callable|mixed $value
     *
     * @return mixed
     */
    function value($value)
    {
        return is_callable($value) ? $value(): $value;
    }
}

// @codingStandardsIgnoreStart
if (!function_exists('__')) {
    /**
     * Translate message
     *
     * Example of usage
     *
     *     // simple
     *     // equal to gettext('Message')
     *     __('Message');
     *
     *     // simple replace of one or more argument(s)
     *     // equal to sprintf(gettext('Message to %s'), 'Username')
     *     __('Message to %s', 'Username');
     *
     * @param  string   $message
     * @param  string[] $text [optional]
     *
     * @return string
     */
    function __($message, ...$text)
    {
        return Translator::translate($message, ...$text);
    }
}

if (!function_exists('_n')) {
    /**
     * Translate plural form
     *
     * Example of usage
     *
     *     // plural form + sprintf
     *     // equal to sprintf(ngettext('%d comment', '%d comments', 4), 4)
     *     _n('%d comment', '%d comments', 4, 4)
     *
     *     // plural form + sprintf
     *     // equal to sprintf(ngettext('%d comment', '%d comments', 4), 4, 'Topic')
     *     _n('%d comment to %s', '%d comments to %s', 4, 'Topic')
     *
     * @param  string   $singular
     * @param  string   $plural
     * @param  integer  $number
     * @param  string[] $text [optional]
     *
     * @return string
     */
    function _n($singular, $plural, $number, ...$text)
    {
        return Translator::translatePlural($singular, $plural, $number, ...$text);
    }
}
// @codingStandardsIgnoreEnd
