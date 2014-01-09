<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Db\Query;

use Bluz\Db\Db;

/**
 * Class Expression
 * @package Bluz\Db\Query
 */
class CompositeBuilder implements \Countable
{
    /**
     * @var string type AND|OR
     */
    private $type;

    /**
     * @var array Parts of the composite expression
     */
    private $parts = array();

    /**
     * Constructor
     *
     * @param array $parts Parts of the composite expression
     * @param string $type AND|OR
     */
    public function __construct(array $parts = array(), $type = 'AND')
    {
        $this->type = (strtoupper($type)=='OR')?'OR':'AND';
        $this->add($parts);
    }

    /**
     * Adds an expression to composite expression.
     *
     * @param mixed $parts
     * @return CompositeBuilder
     */
    public function add($parts)
    {
        foreach ((array) $parts as $part) {
            if (!empty($part) || ($part instanceof self && $part->count() > 0)) {
                $this->parts[] = $part;
            }
        }

        return $this;
    }

    /**
     * Return type of this composite
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Retrieves the amount of expressions on composite expression.
     *
     * @return integer
     */
    public function count()
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
        if (count($this->parts) === 1) {
            return (string) $this->parts[0];
        }
        return '(' . implode(') ' . $this->type . ' (', $this->parts) . ')';
    }
}
