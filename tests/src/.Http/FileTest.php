<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Tests\Http;

use Bluz;
use Bluz\Http\File;
use Bluz\Proxy;
use Bluz\Tests\TestCase;

/**
 * FileTest
 *
 * @package  Bluz\Tests
 *
 * @author   Taras Seryogin
 * @created  24.06.14 12.41
 */
class FileTest extends TestCase
{
    /**
     * @var string Path to test image
     */
    protected $path;

    /**
     * @var Bluz\Http\File
     */
    protected $httpFile;

    /**
     * Reset application
     */
    public static function tearDownAfterClass()
    {
        self::resetApp();
    }

    /**
     * SetUp
     */
    protected function setUp()
    {
        $this->path = Proxy\Config::getData('temp', 'image1');
        $file = array(
            'name' => 'test.jpeg',
            'size' => filesize($this->path),
            'type' => 'image/jpeg',
            'tmp_name' => $this->path,
            'error' => 0
        );
        $this->httpFile =  new File($file);
    }

    /**
     * Test set name
     */
    public function testSetName()
    {
        $result = $this->httpFile->setName('test Image');
        $this->assertInstanceOf('Bluz\Http\File', $result);
    }

    /**
     * Test get name
     */
    public function testGetName()
    {
        $result = $this->httpFile->getName();
        $this->assertEquals('test', $result);
    }


    /**
     * Test get full name
     */
    public function testGetFullName()
    {
        $result = $this->httpFile->getFullName();
        $this->assertEquals('test.jpeg', $result);
    }

    /**
     * Test get extension
     */
    public function testGetExtension()
    {
        $result = $this->httpFile->getExtension();
        $this->assertEquals('jpeg', $result);
    }

    /**
     * Test get type
     */
    public function testGetType()
    {
        $result = $this->httpFile->getType();
        $this->assertEquals('image', $result);
    }

    /**
     * Test get mime type
     */
    public function testGetMimeType()
    {
        $result = $this->httpFile->getMimeType();
        $this->assertEquals('image/jpeg', $result);
    }

    /**
     * Test get error code
     */
    public function testGetErrorCode()
    {
        $result = $this->httpFile->getErrorCode();
        $this->assertEquals(0, $result);
    }

    /**
     * Test get size
     */
    public function testGetSize()
    {
        $result = $this->httpFile->getSize();
        $this->assertEquals(filesize($this->path), $result);
    }
}
