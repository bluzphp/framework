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
namespace Bluz\Translator;

use Bluz\Common\Package;
use Bluz\Config\ConfigException;

/**
 * Translator
 * based on gettext library
 *
 * @category Bluz
 * @package  Translator
 *
 * @author   Anton Shevchuk
 * @created  23.04.13 16:37
 */
class Translator
{
    use Package;

    /**
     * @see http://www.loc.gov/standards/iso639-2/php/code_list.php
     * @var string
     */
    protected $locale = 'en_US';

    /**
     * @var string
     */
    protected $domain = 'messages';

    /**
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
     * checkOptions
     *
     * @throws \Bluz\Config\ConfigException
     * @return boolean
     */
    protected function checkOptions()
    {
        // check path
        if (!is_dir($this->path)) {
            throw new ConfigException("Translator configuration path not found");
        }
        return true;
    }

    /**
     * Initialization
     *
     * @throw \Bluz\Config\ConfigException
     * @return boolean
     */
    protected function initOptions()
    {
        putenv('LC_ALL=' . $this->locale);
        putenv('LANG=' . $this->locale);
        putenv('LANGUAGE=' . $this->locale);

        // Windows workaround
        if (!defined('LC_MESSAGES')) {
            define('LC_MESSAGES', 6);
        }

        setlocale(LC_MESSAGES, $this->locale);

        bindtextdomain($this->domain, $this->path);

        textdomain($this->domain);

        bind_textdomain_codeset($this->domain, 'UTF-8');
    }

    /**
     * translate
     *
     * <code>
     * // simple
     * // equal to gettext('Message')
     * Translator::translate('Message');
     *
     * // simple replace of one or more argument(s)
     * // equal to sprintf(gettext('Message to %s'), 'Username')
     * Translator::translate('Message to %s', 'Username');
     * </code>
     *
     * @param $message
     * @return string
     */
    public static function translate($message)
    {
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
     * translate plural form
     *
     * <code>
     * // plural form + sprintf
     * // equal to sprintf(ngettext('%d comment', '%d comments', 4), 4)
     * Translator::translatePlural('%d comment', '%d comments', 4, 4)
     *
     * // plural form + sprintf
     * // equal to sprintf(ngettext('%d comment', '%d comments', 4), 4, 'Topic')
     * Translator::translatePlural('%d comment to %s', '%d comments to %s', 4, 'Topic')
     * </code>
     * @see http://docs.translatehouse.org/projects/localization-guide/en/latest/l10n/pluralforms.html
     * @param $singular
     * @param $plural
     * @param $number
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
