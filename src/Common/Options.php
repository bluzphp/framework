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
namespace Bluz\Common;

/**
 * Options Trait
 *
 * Example of usage
 *     class Foo
 *     {
 *       use \Bluz\Common\Options;
 *
 *       protected $bar = '';
 *       protected $baz = '';
 *
 *       public function setBar($value)
 *       {
 *           $this->bar = $value;
 *       }
 *
 *       public function setBaz($value)
 *       {
 *           $this->baz = $value;
 *       }
 *     }
 *
 *     $Foo = new Foo(array('bar'=>123, 'baz'=>456));
 *
 * @package  Bluz\Common
 * @link     https://github.com/bluzphp/framework/wiki/Trait-Options
 *
 * @author   Anton Shevchuk
 * @created  12.07.11 16:15
 */
trait Options
{
    /**
     * @var array Options store
     */
    protected $options;

    /**
     * Get option by key
     * @param string $key
     * @param string|null $subKey
     * @return mixed
     */
    public function getOption($key, $subKey = null)
    {
        if (isset($this->options[$key])) {
            if (!is_null($subKey)) {
                return isset($this->options[$key][$subKey])?$this->options[$key][$subKey]:null;
            } else {
                return $this->options[$key];
            }
        } else {
            return null;
        }
    }

    /**
     * Set option by key over setter
     * @param string $key
     * @param string $value
     * @return void
     */
    public function setOption($key, $value)
    {
        $method = 'set' . $this->normalizeKey($key);
        if (method_exists($this, $method)) {
            $this->$method($value);
        } else {
            $this->options[$key] = $value;
        }
    }

    /**
     * Get all options
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Setup, check and init options
     *
     * Requirements
     * - options must be a array
     * - options can be null
     *
     * @param array $options
     * @return self
     */
    public function setOptions($options)
    {
        // store options by default
        $this->options = (array) $options;

        // apply options
        foreach ($this->options as $key => $value) {
            $this->setOption($key, $value);
        }

        // check options
        if ($this->checkOptions()) {
            // initialization
            $this->initOptions();
        }

        return $this;
    }

    /**
     * Check options in package
     * @throws \Bluz\Config\ConfigException
     * @return bool
     */
    protected function checkOptions()
    {
        return true;
    }

    /**
     * Initialization for options
     * @throws \Bluz\Config\ConfigException
     * @return void
     */
    protected function initOptions()
    {
        return;
    }

    /**
     * Normalize key name
     * @param  string $key
     * @return string
     */
    private function normalizeKey($key)
    {
        $option = str_replace(['_', '-'], ' ', strtolower($key));
        $option = str_replace(' ', '', ucwords($option));
        return $option;
    }
}
