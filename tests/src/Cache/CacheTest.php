<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Tests\Cache;

use Bluz\Cache\Cache;
use Bluz\Tests\TestCase;

/**
 * RegistryTest
 *
 * @package  Bluz\Tests
 *
 * @author   Anton Shevchuk
 * @created  08.08.2014 10:58
 */
class CacheTest extends TestCase
{
    /**
     * prepareFileCache
     *
     * @return Cache
     */
    public function prepareFileCache()
    {
        $settings = [
            "adapter" => "phpFile",
            "settings" => [
                "phpFile" => [
                    "cacheDir" => PATH_APPLICATION .'/cache'
                ]
            ]
        ];

        $cache = new Cache();
        $cache->setOptions($settings);

        return $cache;
    }

    /**
     * Cache Throws Exception for invalid configuration
     * @expectedException \Bluz\Common\Exception\ConfigurationException
     */
    public function testCacheWithEmptyConfigurationThrowsException()
    {
        $cache = new Cache();
        $cache->setOptions(array());
    }

    /**
     * Simple Cache test for File adapter
     */
    public function testFileCache()
    {
        $cache = $this->prepareFileCache();

        $cache->set('foo', 'bar');

        $this->assertTrue($cache->contains('foo'));
        $this->assertEquals('bar', $cache->get('foo'));
    }

    /**
     * Cache test for File adapter with tags
     */
    public function testFileCacheWithTags()
    {
        $cache = $this->prepareFileCache();

        $cache->set('foo', 'bar0');
        $cache->set('qux', 'bar1');
        $cache->add('baz', 'bar2');

        $cache->addTag('foo', 'test');
        $cache->addTag('qux', 'test');

        $cache->delete('baz');
        $cache->deleteByTag('test');
        $cache->deleteByTag('unused');

        $this->assertFalse($cache->get('foo'));
        $this->assertFalse($cache->get('qux'));
        $this->assertFalse($cache->get('baz'));
    }

    /**
     * testFileCacheFlush
     */
    public function testFileCacheFlush()
    {
        $cache = $this->prepareFileCache();

        $cache->set('foo', 'bar');
        $cache->flush();

        $this->assertFalse($cache->get('foo'));
    }
}
