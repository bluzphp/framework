<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Tests\Config;

use Bluz;
use Bluz\Config;
use Bluz\Tests;

class ConfigTest extends Bluz\Tests\TestCase
{
    /**
     * @var Config\Config
     */
    protected $config;

    /**
     * @var string Path to config dir
     */
    protected $configPath;

    /**
     * @var string Path to empty config dir
     */
    protected $emptyConfigsDir;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->config = new Bluz\Config\Config();
        $this->configPath = dirname(__FILE__) . '/Fixtures/configs';
        $this->emptyConfigsDir = dirname(__FILE__) . '/Fixtures/emptyConfigsDir';
    }

    /**
     * @covers Bluz\Config\Config::setPath
     * @expectedException Bluz\Config\ConfigException
     */
    public function testSetPathExeption()
    {
        $this->config->setPath('invalid/path');
    }

    /**
     * @covers Bluz\Config\Config::setPath
     */
    public function testSetPath()
    {
        $this->config->setPath($this->configPath);
    }

    /**
     * @covers Bluz\Config\Config::load
     * @expectedException Bluz\Config\ConfigException
     */
    public function testLoadConfigPathIsNotSetup()
    {
        $this->config->load();
    }

    /**
     * @covers Bluz\Config\Config::load
     * @expectedException Bluz\Config\ConfigException
     */
    public function testLoadConfigFileNotFound()
    {
        $this->config->setPath($this->emptyConfigsDir);
        $this->config->load();
    }

    /**
     * @covers Bluz\Config\Config::load
     */
    public function testLoad()
    {
        $this->config->setPath($this->configPath);
        $this->config->load();
    }

    /**
     * @covers Bluz\Config\Config::load
     */
    public function testLoadConfigEnvironmentFileNotFound()
    {
        $this->config->setPath($this->configPath);
        $this->config->load();
        $configWithoutEnvironment = $this->config->getData();
        $this->config->load('not_existed_environment');
        $configWithEnvironment = $this->config->getData();
        $this->assertEquals($configWithoutEnvironment, $configWithEnvironment);
    }

    /**
     * @covers Bluz\Config\Config::load
     */
    public function testLoadWithCfgEnvironment()
    {
        $this->config->setPath($this->configPath);
        $this->config->load();
        $configWithoutEnvironment = $this->config->getData();
        $this->config->load('testing');
        $configWithEnvironment = $this->config->getData();
        $this->assertNotEquals($configWithoutEnvironment, $configWithEnvironment);
    }

    /**
     * @covers Bluz\Config\Config::__get
     */
    public function testGet()
    {
        $this->config->setPath($this->configPath);
        $this->config->load();
        $this->assertEquals(3, $this->config->three);
    }

    /**
     * @covers Bluz\Config\Config::__get
     */
    public function testGetByNotExistedKey()
    {
        $this->config->setPath($this->configPath);
        $this->config->load();
        $this->assertNull($this->config->key_doesnt_exist);
    }

    /**
     * @covers Bluz\Config\Config::__isset
     */
    public function testIsset()
    {
        $this->config->setPath($this->configPath);
        $this->config->load();
        $this->assertTrue(isset($this->config->three));
        $this->assertFalse(isset($this->config->key_doesnt_exist));
    }

    /**
     * @covers Bluz\Config\Config::__isset
     */
    public function testIssetNotExistedKey()
    {
        $this->config->setPath($this->configPath);
        $this->config->load();
        $this->assertFalse(isset($this->config->key_doesnt_exist));
    }

    /**
     * @covers Bluz\Config\Config::__set
     * @expectedException Bluz\Config\ConfigException
     */
    public function testSetReadOnly()
    {
        $this->config->newKey = 'NewValue';
    }

    /**
     * @covers Bluz\Config\Config::getData
     * @expectedException Bluz\Config\ConfigException
     */
    public function testGetDataNotLoadedFromConfig()
    {
        $this->config->setPath($this->configPath);
        $this->config->getData();
    }

    /**
     * @covers Bluz\Config\Config::getData
     */
    public function testGetData()
    {
        $this->config->setPath($this->configPath);
        $this->config->load();
        $this->assertEquals([0, 1, 'three' => 3], $this->config->getData());
    }

    /**
     * @covers Bluz\Config\Config::getData
     */
    public function testGetDataByNotExistedSection()
    {
        $this->config->setPath($this->configPath);
        $this->config->load();
        $this->assertNull($this->config->getData('section_doesnt_exist'));
    }

    /**
     * @covers Bluz\Config\Config::getData
     */
    public function testGetDataBySection()
    {
        $this->config->setPath($this->configPath);
        $this->config->load();
        $this->assertEquals(3, $this->config->getData('three'));
    }

    /**
     * @covers Bluz\Config\Config::getData
     */
    public function testGetDataBySubSection()
    {
        $this->config->setPath($this->configPath);
        $this->config->load('testing');
        $this->assertEquals('4_1', $this->config->getData('section2', 'subsection1'));
    }
}
