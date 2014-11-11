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
namespace Bluz\Translator;

use Bluz\Common\Exception\ConfigurationException;
use Bluz\Common\Options;

/**
 * Translator based on gettext library
 *
 * @package  Bluz\Translator
 * @link     https://github.com/bluzphp/framework/wiki/Translator
 *
 * @author   Anton Shevchuk
 * @created  23.04.13 16:37
 */
class Translator
{
    use Options;

    /**
     * Locale
     * @link http://www.loc.gov/standards/iso639-2/php/code_list.php
     * @var string
     */
    protected $locale = 'en_US';

    /**
     * Text Domain
     * @var string
     */
    protected $domain = 'messages';

    /**
     * Path to text domain files
     * @var string
     */
    protected $path;

    /**
     * set domain
     *
     * @param string $domain
     * @return self
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
        return $this;
    }

    /**
     * set locale
     *
     * @param string $locale
     * @return self
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
        return $this;
    }

    /**
     * set path to l10n
     *
     * @param string $path
     * @return self
     */
    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }

    /**
     * Initialization
     *
     * @throw \Bluz\Config\ConfigException
     * @return void
     */
    protected function initOptions()
    {
        // Setup locale
        putenv('LC_ALL=' . $this->locale);
        putenv('LANG=' . $this->locale);
        putenv('LANGUAGE=' . $this->locale);

        // Windows workaround
        if (!defined('LC_MESSAGES')) {
            define('LC_MESSAGES', 6);
        }

        setlocale(LC_MESSAGES, $this->locale);

        // For gettext only
        if (function_exists('gettext')) {
            // Setup domain path
            $this->addTextDomain($this->domain, $this->path);

            // Setup default domain
            textdomain($this->domain);
        }
    }

    /**
     * Add text domain for gettext
     *
     * @param string $domain of text for gettext setup
     * @param string $path on filesystem
     * @throws ConfigurationException
     * @return self
     */
    public function addTextDomain($domain, $path)
    {
        // check path
        if (!is_dir($path)) {
            throw new ConfigurationException("Translator configuration path `$path` not found");
        }

        bindtextdomain($domain, $path);

        // @todo: hardcoded codeset
        bind_textdomain_codeset($domain, 'UTF-8');

        return $this;
    }

    /**
     * Translate message
     *
     * Simple example of usage
     * equal to <code>gettext('Message')</code>
     *     Translator::translate('Message');
     *
     * Simple replace of one or more argument(s)
     * equal to <code>sprintf(gettext('Message to %s'), 'Username')</code>
     *     Translator::translate('Message to %s', 'Username');
     *
     * @api
     * @param string $message
     * @return string
     */
    public static function translate($message)
    {
        if (empty($message)) {
            return $message;
        }

        if (function_exists('gettext')) {
            $message = gettext($message);
        }

        if (func_num_args() > 1) {
            $args = array_slice(func_get_args(), 1);
            $message = vsprintf($message, $args);
        }

        return $message;
    }

    /**
     * Translate plural form
     *
     * Example of usage plural form + sprintf
     * equal to <code>sprintf(ngettext('%d comment', '%d comments', 4), 4)</code>
     *     Translator::translatePlural('%d comment', '%d comments', 4, 4)
     *
     * Example of usage plural form + sprintf
     * equal to <code>sprintf(ngettext('%d comment', '%d comments', 4), 4, 'Topic')</code>
     *     Translator::translatePlural('%d comment to %s', '%d comments to %s', 4, 'Topic')
     *
     * @api
     * @link http://docs.translatehouse.org/projects/localization-guide/en/latest/l10n/pluralforms.html
     * @param string $singular
     * @param string $plural
     * @param integer $number
     * @return string
     */
    public static function translatePlural($singular, $plural, $number)
    {
        if (function_exists('ngettext')) {
            $message = ngettext($singular, $plural, $number);
        } else {
            $message = $singular;
        }

        if (func_num_args() > 3) {
            $args = array_slice(func_get_args(), 3);
            $message = vsprintf($message, $args);
        }

        return $message;
    }
}
