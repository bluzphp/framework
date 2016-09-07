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
 *     $Foo = new Foo(['bar'=>123, 'baz'=>456]);
 *
 * @package  Bluz\Common
 * @author   Anton Shevchuk
 * @link     https://github.com/bluzphp/framework/wiki/Trait-Options
 */
trait Options
{
    /**
     * @var array options store
     */
    protected $options;

    /**
     * Get option by key
     *
     * @param  string      $key
     * @param  string|null $section
     * @return mixed
     */
    public function getOption($key, $section = null)
    {
        if (isset($this->options[$key])) {
            if (!is_null($section)) {
                return isset($this->options[$key][$section])?$this->options[$key][$section]:null;
            } else {
                return $this->options[$key];
            }
        } else {
            return null;
        }
    }

    /**
     * Set option by key over setter
     *
     * @param  string $key
     * @param  string $value
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
     *
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
     * @param  array $options
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

        // check and initialize options
        $this->initOptions();

        return $this;
    }

    /**
     * Check and initialize options in package
     *
     * @throws \Bluz\Config\ConfigException
     * @return void
     */
    protected function initOptions()
    {
        return;
    }

    /**
     * Normalize key name
     *
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
