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
    protected $path;

    /**
     * @var string Path to empty config dir
     */
    protected $emptyConfigsDir;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        $this->path = __DIR__ . '/Fixtures/';
        $this->emptyConfigsDir = __DIR__ . '/Fixtures/emptyConfigsDir';
    }

    /**
     * @covers \Bluz\Config\ConfigLoader::setPath
     */
    public function testSetPathExeption(): void
    {
        $this->expectException(ConfigException::class);
        $loader = new Config\ConfigLoader();
        $loader->setPath('invalid/path');
    }

    /**
     * @covers \Bluz\Config\ConfigLoader::setPath
     */
    public function testSetPath(): void
    {
        $loader = new Config\ConfigLoader();
        $loader->setPath($this->path);
    }

    /**
     * @covers \Bluz\Config\ConfigLoader::load
     */
    public function testLoadConfigPathIsNotSetup(): void
    {
        $this->expectException(ConfigException::class);
        $loader = new Config\ConfigLoader();
        $loader->load();
    }

    /**
     * @covers \Bluz\Config\ConfigLoader::load
     */
    public function testLoadConfigFileNotFound(): void
    {
        $this->expectException(ConfigException::class);
        $loader = new Config\ConfigLoader();
        $loader->setPath($this->emptyConfigsDir);
        $loader->load();
    }

    /**
     * @covers \Bluz\Config\ConfigLoader::load
     */
    public function testLoad(): void
    {
        $loader = new Config\ConfigLoader();
        $loader->setPath($this->path);
        $loader->load();
    }

    /**
     * @covers \Bluz\Config\ConfigLoader::load
     */
    public function testLoadConfigWithEnvironment(): void
    {
        $loader = new Config\ConfigLoader();
        $loader->setPath($this->path);
        $loader->load();
        $configWithoutEnvironment = $loader->getConfig();

        $loader2 = new Config\ConfigLoader();
        $loader2->setPath($this->path);
        $loader2->setEnvironment('testing');
        $loader2->load();
        $configWithEnvironment = $loader2->getConfig();

        self::assertNotEquals($configWithoutEnvironment, $configWithEnvironment);
    }

    /**
     * @covers \Bluz\Config\ConfigLoader::load
     */
    public function testLoadConfigWithWrongEnvironment(): void
    {
        $this->expectException(ConfigException::class);
        $loader = new Config\ConfigLoader();
        $loader->setPath($this->path);
        $loader->setEnvironment('not_existed_environment');
        $loader->load();
    }
}
