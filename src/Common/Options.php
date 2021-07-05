<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Common;

use Bluz\Collection\Collection;

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
 *     $Foo = new Foo();
 *     $Foo->setOptions(['bar'=>123, 'baz'=>456]);
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
    protected $options = [];

    /**
     * Get option by key
     *
     * @param  string $key
     * @param  array  $keys
     *
     * @return mixed
     */
    public function getOption(string $key, ...$keys)
    {
        $method = 'get' . Str::toCamelCase($key);
        if (method_exists($this, $method)) {
            return $this->$method($key, ...$keys);
        }
        return Collection::get($this->options, $key, ...$keys);
    }

    /**
     * Set option by key over setter
     *
     * @param  string $key
     * @param  mixed $value
     *
     * @return void
     */
    public function setOption(string $key, $value): void
    {
        $method = 'set' . Str::toCamelCase($key);
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
    public function getOptions(): array
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
     * @param array|null $options
     *
     * @return void
     */
    public function setOptions(?array $options = null): void
    {
        // store options by default
        $this->options = (array)$options;

        // apply options
        foreach ($this->options as $key => $value) {
            $this->setOption($key, $value);
        }
    }
}
