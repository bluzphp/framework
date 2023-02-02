<?php

/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

namespace Bluz\Tests\Unit\Config;

use Bluz;
use Bluz\Config;
use Bluz\Config\ConfigException;
use Bluz\Tests\Unit\Unit;

class ConfigTest extends Unit
{
    /**
     * @var string Path to config dir
     */
    protected string $path;

    /**
     * @var string Path to config dir
     */
    protected string $testing;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        $this->path = __DIR__ . '/Fixtures/configs/default';
        $this->testing = __DIR__ . '/Fixtures/configs/testing';
    }

    /**
     * @covers \Bluz\Config\Config::get
     * @throws ConfigException
     */
    public function testGetData(): void
    {
        $loader = new Config\ConfigLoader();
        $loader->load($this->path);

        $config = new Config\Config();
        $config->setFromArray($loader->getConfig());

        self::assertEquals(
            ['application' => ['section1' => 'default', 'section2' => [], 'section3' => []]],
            $config->get()
        );
    }

    /**
     * @covers \Bluz\Config\Config::get
     * @throws ConfigException
     */
    public function testGetDataByNotExistedSection(): void
    {
        $loader = new Config\ConfigLoader();
        $loader->load($this->path);

        $config = new Config\Config();
        $config->setFromArray($loader->getConfig());

        self::assertNull($config->get('section_doesnt_exist'));
    }

    /**
     * @covers \Bluz\Config\Config::get
     * @throws ConfigException
     */
    public function testGetDataBySection(): void
    {
        $loader = new Config\ConfigLoader();
        $loader->load($this->path);

        $config = new Config\Config();
        $config->setFromArray($loader->getConfig());

        self::assertEquals(
            ['section1' => 'default', 'section2' => [], 'section3' => []],
            $config->get('application')
        );
    }

    /**
     * @covers \Bluz\Config\Config::get
     * @throws ConfigException
     */
    public function testGetDataBySubSection(): void
    {
        $loader = new Config\ConfigLoader();
        $loader->load($this->path);
        $loader->load($this->testing);

        $config = new Config\Config();
        $config->setFromArray($loader->getConfig());

        self::assertEquals(1, $config->get('application', 'section1'));
    }
}
