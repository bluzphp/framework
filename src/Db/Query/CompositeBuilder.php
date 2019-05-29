<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Db\Query;

/**
 * Class Expression Builder
 *
 * @package Bluz\Db\Query
 */
class CompositeBuilder implements \Countable
{
    /**
     * @var string type AND|OR
     */
    private $type;

    /**
     * @var array parts of the composite expression
     */
    private $parts = [];

    /**
     * Constructor
     *
     * @param array  $parts parts of the composite expression
     * @param string $type  AND|OR
     */
    public function __construct(array $parts = [], string $type = 'AND')
    {
        $type = strtoupper($type);
        $this->type = $type === 'OR' ? 'OR' : 'AND';
        $this->addParts($parts);
    }

    /**
     * Adds a set of expressions to composite expression
     *
     * @param  array $parts
     *
     * @return CompositeBuilder
     */
    public function addParts($parts): CompositeBuilder
    {
        foreach ($parts as $part) {
            $this->addPart($part);
        }

        return $this;
    }

    /**
     * Adds an expression to composite expression
     *
     * @param  mixed $part
     *
     * @return CompositeBuilder
     */
    public function addPart($part): CompositeBuilder
    {
        if (!empty($part) || ($part instanceof self && $part->count() > 0)) {
            $this->parts[] = $part;
        }
        return $this;
    }

    /**
     * Return type of this composite
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Retrieves the amount of expressions on composite expression.
     *
     * @return integer
     */
    public function count(): int
    {
        return count($this->parts);
    }

    /**
     * Retrieve the string representation of this composite expression.
     *
     * @return string
     */
    public function __toString()
    {
        if ($this->count() === 1) {
            return (string) $this->parts[0];
        }
        return '(' . implode(') ' . $this->type . ' (', $this->parts) . ')';
    }
}
