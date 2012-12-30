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
 * APC cache adapter
 * @author murzik
 */
class ApcAdapter extends AdapterBase
{
    public function __construct()
    {
        if(!extension_loaded('apc')) {
            $msg = "APC extension not installed/enabled.
                    Install and/or enable APC extension. See phpinfo() for more information";
            throw new CacheException($msg);
        }
    }
    protected function doGet($id)
    {
        return apc_fetch($id);
    }

    protected function doAdd($id, $data, $ttl = 0)
    {
        return apc_add($id, $data, $ttl);
    }

    protected function doSet($id, $data, $ttl = 0)
    {
        return apc_store($id, $data, $ttl);
    }

    protected function doContains($id)
    {
        return apc_exists($id);
    }

    protected function doDelete($id)
    {
        return apc_delete($id);
    }

    protected function doFlush()
    {
        return apc_clear_cache("user");
    }
}