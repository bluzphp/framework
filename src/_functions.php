<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

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
     * @param $params
     */
    function debug(...$params)
    {
        // check definition
        if (!getenv('BLUZ_DEBUG')) {
            return;
        }

        ini_set('xdebug.var_display_max_children', 512);

        if ('cli' == PHP_SAPI) {
            if (extension_loaded('xdebug')) {
                // try to enable CLI colors
                ini_set('xdebug.cli_color', 1);
                xdebug_print_function_stack();
            } else {
                debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
            }
            var_dump($params);
        } else {
            echo '<div class="textleft clear"><pre>';
            debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
            var_dump($params);
            echo '</pre></div>';
        }
    }
}

if (!function_exists('app')) {
    /**
     * Alias for call instance of Application
     *
     * @return \Bluz\Application\Application
     */
    function app()
    {
        return \Bluz\Application\Application::getInstance();
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
     * @param string $variable
     * @param integer $flags
     * @return string
     */
    function esc($variable, $flags = ENT_HTML5)
    {
        return htmlentities($variable, $flags, "UTF-8");
    }
}

// @codingStandardsIgnoreStart
if (!function_exists('__')) {
    /**
     * Translate message
     *
     * Example of usage
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
     * @param  integer      $number
     * @param  string[] $text      [optional]
     * @return string
     */
    function _n($singular, $plural, $number, ...$text)
    {
        return Translator::translatePlural($singular, $plural, $number, ...$text);
    }
}
// @codingStandardsIgnoreEnd
