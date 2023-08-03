<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Db;

/**
 * Db Table Row
 *
 * @package  Bluz\Db
 * @author   Anton Shevchuk
 * @link     https://github.com/bluzphp/framework/wiki/Db-Row
 */
abstract class Row implements RowInterface
{
    /**
     * Create Row instance
     *
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        // not clean data, but not modified
        if (count($data)) {
            $this->setFromArray($data);
        }
    }

    /**
     * @param $key
     * @return mixed
     */
    public function __get($key)
    {
        if (property_exists($this, $key)) {
            return $this->{$key};
        }
        return null;
    }

    /**
     * @param $key
     * @param $value
     * @return void
     */
    public function __set($key, $value): void
    {
        if (property_exists($this, $key)) {
            $this->{$key} = $value;
        }
    }

    /**
     * @param $key
     * @return bool
     */
    public function __isset($key): bool
    {
        return property_exists($this, $key);
    }

    /**
     * @param string $key
     * @return void
     */
    public function __unset(string $key): void
    {
        unset($this->{$key});
    }

    /**
     * @inheritDoc
     */
    public function setFromArray(array $data): void
    {
        foreach ($data as $key => $value) {
            $this->__set($key, $value);
        }
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
