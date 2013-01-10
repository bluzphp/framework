<?php
/**
 * Copyright (c) 2012 by Bluz PHP Team
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
namespace Bluz\View;

use Bluz\Application;

/**
 * View
 *
 * @category Bluz
 * @package  View
 *
 * @method string ahref(\string $text, \string $href, array $attributes = [])
 * @method string api(\string $module, \string $method, $params = array())
 * @method string baseUrl(\string $file = null)
 * @method array|null breadCrumbs(array $data = [])
 * @method string|boolean controller(\string $controller = null)
 * @method string|View dispatch($module, $controller, $params = array())
 * @method string|null headScript(\string $script = null)
 * @method string|null headStyle(\string $style = null, $media = 'all')
 * @method string|View meta(\string $name = null, string $content = null)
 * @method string|boolean module(\string $module = null)
 * @method string|View link(string $src = null, string $rel = 'stylesheet')
 * @method void partial($__template, $__params = array())
 * @method void partialLoop($template, $data = [], $params = [])
 * @method string script(\string $script)
 * @method string style(\string $style, $media = 'all')
 * @method string|View title(\string $title = null, $position = 'replace', $separator = ' :: ')
 * @method string|View url(\string $module, \string $controller, array $params = [], boolean $checkAccess = false)
 * @method \Bluz\Auth\AbstractEntity|null user()
 * @method void widget($module, $widget, $params = [])
 *
 * @author   Anton Shevchuk, ErgallM
 * @created  08.07.11 11:49
 */
class View
{
    use \Bluz\Package;
    use \Bluz\Helper;

    /**
     * Constants for define positions
     */
    const POS_PREPEND = 'prepend';
    const POS_REPLACE = 'replace';
    const POS_APPEND  = 'append';

    /**
     * @var string
     */
    protected $baseUrl;

    /**
     * View variables
     *
     * @var array
     */
    protected $data = array();

    /**
     * System variables, should be uses for helpers
     *
     * @var array
     */
    protected $system = array();

    /**
     * @var string path to template
     */
    protected $path;

    /**
     * @var string
     */
    protected $template;

    /**
     * __construct
     *
     * @return self
     */
    public function __construct()
    {
        // initial default helper path
        $this->addHelperPath(dirname(__FILE__) . '/Helper/');
    }

    /**
     * __sleep
     *
     * @return array
     */
    public function __sleep()
    {
        return ['baseUrl', 'data', 'system', 'path', 'template'];
    }

    /**
     * Get a variable
     *
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        if (isset($this->data[$key])) {
            return $this->data[$key];
        } else {
            return null;
        }
    }


    /**
     * Is set a variable
     *
     * @param string $key
     * @return mixed
     */
    public function __isset($key)
    {
        return isset($this->data[$key]);
    }

    /**
     * Assign a variable
     *
     * A $value of null will unset the $key if it exists
     *
     * @param string $key
     * @param mixed $value
     * @return View
     */
    public function __set($key, $value)
    {
        $key = (string) $key;

        if ((null === $value) && isset($this->data[$key])) {
            unset($this->data[$key]);
        } elseif (null !== $value) {
            $this->data[$key] = $value;
        }

        return $this;
    }

    /**
     * set data from array
     *
     * @param array $data
     * @return View
     */
    public function setData($data = array())
    {
        $this->data = array_merge($this->data, $data);
        return $this;
    }

    /**
     * merge data from array
     *
     * @param array $data
     * @return View
     */
    public function mergeData($data = array())
    {
        $this->data = $this->mergeArrays($this->data, $data);
        return $this;
    }

    /**
     * @param $array1
     * @param $array2
     * @return array
     */
    protected function mergeArrays($array1, $array2)
    {
        foreach ($array2 as $key => $value) {
            if (array_key_exists($key, $array1) && is_array($value)) {
                $array1[$key] = $this->mergeArrays($array1[$key], $array2[$key]);
            } else {
                $array1[$key] = $value;
            }
        }
        return $array1;
    }

    /**
     * is callable
     *
     * @return string
     */
    public function __invoke()
    {
        return $this->render();
    }

    /**
     * render like string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * toArray
     *
     * @return array
     */
    public function toArray()
    {
        return $this->data;
    }

    /**
     * setup path
     *
     * @param string $path
     * @return View
     */
    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }

    /**
     * setup template
     *
     * @param string $file
     * @return View
     */
    public function setTemplate($file)
    {
        $this->template = $file;
        return $this;
    }

    /**
     * manipulation under system stack
     *
     * @param string $key
     * @param mixed|null $value
     * @return mixed|View
     */
    protected function system($key, $value = null)
    {
        if (null === $value) {
            if (isset($this->system[$key])) {
                return $this->system[$key];
            } else {
                return null;
            }
        } else {
            $this->system[$key] = $value;
        }
        return $this;
    }

    /**
     * Simple translate/formatter wrapper
     *
     * <code>
     * // simple
     * // equal to gettext('Message')
     * __('Message');
     *
     * // simple replace of one argument
     * // equal to sprintf(gettext('Message to %s'), 'Username')
     * __('Message to %s', 'Username');
     *
     * // plural form + sprintf
     * // equal to sprintf(ngettext('%d comment', '%d comments', 4), 4)
     * __('%d comment', '%d comments', 4)
     *
     * // plural form + sprintf
     * // equal to sprintf(ngettext('%d comment', '%d comments', 4), 4, 'Topic')
     * __('%d comment to %s', '%d comments to %s', 4, 'Topic')
     * </code>
     *
     * @param string $message
     * @return string
     */
    public function __($message) {

        if (func_num_args() == 1) {
            if (function_exists('gettext')) {
                $message = gettext($message);
            }
            return $message;
        } elseif (func_num_args() == 2) {
            // simple replace
            $args = func_get_args();
            if (function_exists('gettext')) {
                $message = gettext($message);
            }
            return sprintf($message, $args[1]);
        } elseif (func_num_args() == 3) {
            // plural form
            $args = func_get_args();
            if (function_exists('ngettext')) {
                $message = ngettext($message, $args[1], $args[2]);
            }
            return sprintf($message, $args[2]);
        } elseif (func_num_args() > 3) {
            // plural form with additional params
            if (function_exists('ngettext')) {
                $message = call_user_func_array('ngettext', func_get_args());
            }
            $args = array_slice(func_get_args(), 2);
            return vsprintf($message, $args);
        }
    }

    /**
     * Render
     *
     * @throws ViewException
     * @return string
     */
    public function render()
    {
        ob_start();
        try {
            if (!file_exists($this->path .'/'. $this->template)) {
                throw new ViewException("Template '{$this->template}' not found");
            }
            extract($this->data);
            require $this->path .'/'.  $this->template;
        } catch (\Exception $e) {
            ob_get_clean();
            if (DEBUG) {
                echo $e->getMessage();
                var_dump($e->getTraceAsString());
            }
            // nothing for production
            return '';
        }
        $content = ob_get_clean();
        return (string) $content;
    }
}