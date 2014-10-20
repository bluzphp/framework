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
namespace Bluz\Cache;

/**
 * Interface for Tagable classes
 *
 * @package Bluz\Cache
 * @author murzik
 */
interface TagableInterface
{
    /**
     * Add tag $tag for cache entry with $id identifier
     * @param string $id
     * @param string $tag
     * @return bool
     */
    public function addTag($id, $tag);

    /**
     * Delete all cache entries associated with given $tag
     * @param string $tag
     * @return bool
     */
    public function deleteByTag($tag);
}
