<?php
/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */

namespace Bluz\Tests\Config;

use Bluz;
use Bluz\Config;
use Bluz\Tests\FrameworkTestCase;

class ConfigTest extends FrameworkTestCase
{
    /**
     * @var string Path to config dir
     */
    protected $path;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() : void
    {
        $this->path = __DIR__ . '/Fixtures/';
    }

    /**
     * @covers \Bluz\Config\Config::get
     */
    public function testGetData() : void
    {
        $loader = new Config\ConfigLoader();
        $loader->setPath($this->path);
        $loader->load();

        $config = new Config\Config();
        $config->setFromArray($loader->getConfig());

        self::assertEquals(
            ['application' => ['section1' => 'default', 'section2' => [], 'section3' => []]],
            $config->get()
        );
    }

    /**
     * @covers \Bluz\Config\Config::get
     */
    public function testGetDataByNotExistedSection() : void
    {
        $loader = new Config\ConfigLoader();
        $loader->setPath($this->path);
        $loader->load();

        $config = new Config\Config();
        $config->setFromArray($loader->getConfig());

        self::assertNull($config->get('section_doesnt_exist'));
    }

    /**
     * @covers \Bluz\Config\Config::get
     */
    public function testGetDataBySection() : void
    {
        $loader = new Config\ConfigLoader();
        $loader->setPath($this->path);
        $loader->load();

        $config = new Config\Config();
        $config->setFromArray($loader->getConfig());

        self::assertEquals(
            ['section1' => 'default', 'section2' => [], 'section3' => []],
            $config->get('application')
        );
    }

    /**
     * @covers \Bluz\Config\Config::get
     */
    public function testGetDataBySubSection() : void
    {

        $loader = new Config\ConfigLoader();
        $loader->setPath($this->path);
        $loader->setEnvironment('testing');
        $loader->load();

        $config = new Config\Config();
        $config->setFromArray($loader->getConfig());

        self::assertEquals(1, $config->get('application', 'section1'));
    }
}
