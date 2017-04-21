<?php
/**
 * @namespace
 */

namespace Bluz\Common;

/**
 * Collection
 *
 * @package  Bluz\Common
 * @author   Anton Shevchuk
 */
class Collection
{
    /**
     * Get an element to array by key and sub-keys
     *
     * @param array $array
     * @param array ...$keys
     * @return mixed|null
     */
    static public function get(array $array, ...$keys)
    {
        $key = array_shift($keys);

        if (!isset($array[$key])) {
            return null;
        }

        if (empty($keys)) {
            return $array[$key] ?? null;
        }

        if (!is_array($array[$key])) {
            return null;
        }

        return self::get($array[$key], ...$keys);
    }

    /**
     * Check an element of array by key and sub-keys
     *
     * @param array $array
     * @param array ...$keys
     * @return bool
     */
    static public function has(array $array, ...$keys) : bool
    {
        $key = array_shift($keys);

        if (!isset($array[$key])) {
            return false;
        }

        if (empty($keys)) {
            return isset($array[$key]);
        }

        if (!is_array($array[$key])) {
            return false;
        }

        return self::has($array[$key], ...$keys);
    }

    /**
     * Add an element of array by key and sub-keys
     *
     * @param array $array
     * @param array ...$keys
     * @return void
     * @throws \InvalidArgumentException
     */
    static public function set(array &$array, ...$keys)
    {
        if (count($keys) < 2) {
            throw new \InvalidArgumentException('Method `Collection::set()` is required minimum one key and value');
        }

        $value = array_pop($keys);
        while (count($keys) > 1) {
            $key = array_shift($keys);
            if (! isset($array[$key]) || ! is_array($array[$key])) {
                $array[$key] = [];
            }
            $array = &$array[$key];
        }
        $array[array_shift($keys)] = $value;
    }
}
