<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Cache\Adapter;

use Bluz\Cache\Cache;
use Bluz\Cache\InvalidArgumentException;

/**
 * Adapter that caches data into php array.
 * It can cache data that support var_export.
 * This adapter very fast and cacheable by opcode cachers but it have some limitations related to var_export.
 * It's best to use for scalar data caching
 * @see more at http://php.net/manual/en/function.var-export.php
 * @see more at http://php.net/manual/en/language.oop5.magic.php#object.set-state
 * @author murzik
 */
class PhpFile extends FileBase
{
    protected $data = array();

    /**
     * {@inheritdoc}
     */
    protected function doContains($id)
    {
        $filename = $this->getFilename($id);

        if (!is_file($filename)) {
            return false;
        }

        $cacheEntry = include $filename;

        return $cacheEntry['ttl'] === Cache::TTL_NO_EXPIRY || $cacheEntry['ttl'] > time();
    }

    /**
     * {@inheritdoc}
     */
    protected function doGet($id)
    {
        $filename = $this->getFilename($id);

        if (!is_file($filename)) {
            return false;
        }

        $cacheEntry = include $filename;

        if ($cacheEntry['ttl'] !== Cache::TTL_NO_EXPIRY && $cacheEntry['ttl'] < time()) {
            return false;
        }

        return $cacheEntry['data'];
    }

    /**
     * {@inheritdoc}
     */
    protected function doSet($id, $data, $ttl = Cache::TTL_NO_EXPIRY)
    {
        if ($ttl > 0) {
            $ttl = time() + $ttl;
        }

        //if we have an array containing objects - we will have a problem.
        if (is_object($data) && !method_exists($data, '__set_state')) {
            throw new InvalidArgumentException(
                "Invalid argument given, PhpFileAdapter only allows objects that implement __set_state() " .
                "and fully support var_export()."
            );
        }

        $fileName = $this->getFilename($id);
        $filePath = pathinfo($fileName, PATHINFO_DIRNAME);

        if (!is_dir($filePath)) {
            mkdir($filePath, 0777, true);
        }

        $cacheEntry = array(
            'ttl' => $ttl,
            'data' => $data
        );

        $cacheEntry = var_export($cacheEntry, true);
        $code = sprintf('<?php return %s;', $cacheEntry);

        return file_put_contents($fileName, $code);
    }

    /**
     * {@inheritdoc}
     */
    protected function doAdd($id, $data, $ttl = Cache::TTL_NO_EXPIRY)
    {
        if (!$this->doContains($id)) {
            return $this->doSet($id, $data, $ttl);
        } else {
            return false;
        }
    }
}
