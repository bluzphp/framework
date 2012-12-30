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

namespace Bluz\Cache;
/**
 * Bluz cache driver interface
 * @author Murzik
 */
interface CacheInterface
{
    /**
     * Retrieve data from cache by identifier
     * @param string $id cache entry identifier
     * @return mixed
     */
    public function get($id);

    /**
     * Put data into cache.
     * Overwrite cache entry with given id if it exists.
     * @param string $id cache entry identifier
     * @param mixed $data data to cache
     * @param int $ttl Time To Live in seconds 0 == infinity
     * @return boolean
     */
    public function set($id, $data, $ttl = 0);

    /**
     * Put data into cache.
     * Operation will fail if cache entry with given id already exists
     * @param string $id cache entry identifier
     * @param mixed $data data to cache
     * @param int $ttl Time To Live in seconds 0 == infinity
     * @return boolean
     */
    public function add($id, $data, $ttl = 0);

    /**
     * Test for cache entry existence
     * @param string $id cache entry identifier
     * @return boolean
     */
    public function contains($id);


    /**
     * Delete cache entry
     * @param string $id
     * @return boolean
     */
    public function delete($id);

    /**
     * Invalidate(delete) all cache entries
     * @return void
     */
    public function flush();
}