<?php

/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

namespace Bluz\Tests\Config;

use Bluz;
use Bluz\Config;
use Bluz\Config\ConfigException;
use Bluz\Tests\FrameworkTestCase;

class ConfigLoaderTest extends FrameworkTestCase
{
    /**
     * @var string Path to config dir
     */
    protected string $path;

    /**
     * @var string Path to empty config dir
     */
    protected string $emptyConfigsDir;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        $this->path = __DIR__ . '/Fixtures/configs/';
    }

    public function testEmptyPath(): void
    {
        $this->expectException(ConfigException::class);
        $loader = new Config\ConfigLoader();
        $loader->load('');
    }

    /**
     * @covers \Bluz\Config\ConfigLoader::setPath
     */
    public function testLoadNotExists(): void
    {
        $this->expectException(ConfigException::class);
        $loader = new Config\ConfigLoader();
        $loader->load('invalid/path');
    }

    /**
     * @covers \Bluz\Config\ConfigLoader::load
     */
    public function testLoad(): void
    {
        $loader = new Config\ConfigLoader();
        $loader->load($this->path.'/default');
    }

    /**
     * @covers \Bluz\Config\ConfigLoader::load
     */
    public function testLoadAndMerge(): void
    {
        $loader = new Config\ConfigLoader();
        $loader->load($this->path .'/default');
        $configWithoutEnvironment = $loader->getConfig();

        $loader->load($this->path .'/testing');
        $configWithEnvironment = $loader->getConfig();

        self::assertArrayHasSize($configWithoutEnvironment, 1);
        self::assertArrayHasSize($configWithEnvironment, 2);
    }
}
