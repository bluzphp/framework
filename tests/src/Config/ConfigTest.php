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
use Bluz\Tests\TestCase;

class ConfigTest extends TestCase
{
    /**
     * @var Config\Config
     */
    protected $config;

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
    protected function setUp()
    {
        $this->config = new Bluz\Config\Config();
        $this->path = dirname(__FILE__) . '/Fixtures/';
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
        $this->config->setPath($this->path);
    }

    /**
     * @covers Bluz\Config\Config::init
     * @expectedException \Bluz\Config\ConfigException
     */
    public function testLoadConfigPathIsNotSetup()
    {
        $this->config->init();
    }

    /**
     * @covers Bluz\Config\Config::init
     * @expectedException \Bluz\Config\ConfigException
     */
    public function testLoadConfigFileNotFound()
    {
        $this->config->setPath($this->emptyConfigsDir);
        $this->config->init();
    }

    /**
     * @covers Bluz\Config\Config::init
     */
    public function testLoad()
    {
        $this->config->setPath($this->path);
        $this->config->init();
    }

    /**
     * @covers Bluz\Config\Config::init
     */
    public function testLoadConfigWithEnvironment()
    {
        $this->config->setPath($this->path);
        $this->config->init();
        $configWithoutEnvironment = $this->config->getData();
        $this->config->setEnvironment('testing');
        $this->config->init();
        $configWithEnvironment = $this->config->getData();
        $this->assertNotEquals($configWithoutEnvironment, $configWithEnvironment);
    }

    /**
     * @covers Bluz\Config\Config::init
     * @expectedException \Bluz\Config\ConfigException
     */
    public function testLoadConfigWithWrongEnvironment()
    {
        $this->config->setPath($this->path);
        $this->config->setEnvironment('not_existed_environment');
        $this->config->init();
    }

    /**
     * @covers Bluz\Config\Config::getData
     * @expectedException \Bluz\Config\ConfigException
     */
    public function testGetDataNotLoadedFromConfig()
    {
        $this->config->setPath($this->path);
        $this->config->getData();
    }

    /**
     * @covers Bluz\Config\Config::getData
     */
    public function testGetData()
    {
        $this->config->setPath($this->path);
        $this->config->init();
        $this->assertEquals(
            ['application' => ['section1'=>'default', 'section2'=>[], 'section3'=>[]]],
            $this->config->getData()
        );
    }

    /**
     * @covers Bluz\Config\Config::getData
     */
    public function testGetDataByNotExistedSection()
    {
        $this->config->setPath($this->path);
        $this->config->init();
        $this->assertNull($this->config->getData('section_doesnt_exist'));
    }

    /**
     * @covers Bluz\Config\Config::getData
     */
    public function testGetDataBySection()
    {
        $this->config->setPath($this->path);
        $this->config->init();
        $this->assertEquals(
            ['section1'=>'default', 'section2'=>[], 'section3'=>[]],
            $this->config->getData('application')
        );
    }

    /**
     * @covers Bluz\Config\Config::getData
     */
    public function testGetDataBySubSection()
    {
        $this->config->setPath($this->path);
        $this->config->setEnvironment('testing');
        $this->config->init();
        $this->assertEquals(1, $this->config->getData('application', 'section1'));
    }


    /**
     * @covers Bluz\Config\Config::getModuleData
     */
    public function testGetModuleData()
    {
        $this->config->setPath($this->path);
        $this->assertEquals(
            ['foo' => 'bar', 'qux' => 'bar'],
            $this->config->getModuleData('index')
        );
    }

    /**
     * @covers Bluz\Config\Config::getModuleData
     * @expectedException \Bluz\Config\ConfigException
     */
    public function testGetModuleDataByNotExistedModule()
    {
        $this->config->setPath($this->path);
        $this->assertNull($this->config->getModuleData('module_doesnt_exist'));
    }

    /**
     * @covers Bluz\Config\Config::getModuleData
     */
    public function testGetModuleDataBySection()
    {
        $this->config->setPath($this->path);
        $this->assertEquals('bar', $this->config->getModuleData('index', 'foo'));
    }


    /**
     * @covers Bluz\Config\Config::getModuleData
     */
    public function testGetModuleDataByNotExistedSection()
    {
        $this->config->setPath($this->path);
        $this->assertNull($this->config->getModuleData('index', 'section_doesnt_exist'));
    }

    /**
     * @covers Bluz\Config\Config::getModuleData
     */
    public function testGetModuleDataBySectionWithEnvironment()
    {
        $this->config->setPath($this->path);
        $this->config->setEnvironment('testing');
        $this->assertEquals('baz', $this->config->getModuleData('index', 'foo'));
    }
}
