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
 * Example
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
 * @author   Anton Shevchuk
 * @created  12.07.11 16:15
 */
trait Options
{
    /**
     * Options store
     * @var array
     */
    protected $options;

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
            $method = 'set' . $this->normalizeKey($key);
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }

        // check options
        if ($this->checkOptions()) {
            // initialization
            $this->initOptions();
        }

        return $this;
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
     * Validation
     *
     * @throws \Bluz\Config\ConfigException
     * @return boolean
     */
    protected function checkOptions()
    {
        return true;
    }

    /**
     * Initialization
     *
     * @throws \Bluz\Config\ConfigException
     * @return void
     */
    protected function initOptions()
    {
        return;
    }

    /**
     * Get option by key
     *
     * @param string $key
     * @return mixed
     */
    public function getOption($key)
    {
        if (isset($this->options[$key])) {
            return $this->options[$key];
        } else {
            return false;
        }
    }

    /**
     * Normalize key name
     *
     * @param  $key
     * @return mixed
     */
    private function normalizeKey($key)
    {
        $option = str_replace('_', ' ', strtolower($key));
        $option = str_replace(' ', '', ucwords($option));
        return $option;
    }
}
