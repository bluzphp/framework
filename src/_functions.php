<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * Simple functions of framework
 * be careful with this way
 * @author   Anton Shevchuk
 * @created  07.09.12 11:29
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
     * @return void
     */
    function debug()
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
            var_dump(func_get_args());
        } else {
            echo '<div class="textleft clear"><pre>';
            debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
            var_dump(func_get_args());
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
     * @param int $flags
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
     * @param string $message,...
     * @return string
     */
    function __()
    {
        return call_user_func_array(['\Bluz\Translator\Translator', 'translate'], func_get_args());
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
     * @param string $singular
     * @param string $plural
     * @param integer $number,...
     * @return string
     */
    function _n()
    {
        return call_user_func_array(['\Bluz\Translator\Translator', 'translatePlural'], func_get_args());
    }
}
// @codingStandardsIgnoreEnd
