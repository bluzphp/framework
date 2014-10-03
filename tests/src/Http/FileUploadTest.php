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
use Bluz\Proxy;
use Bluz\Proxy\Request;

/**
 * FileUploadTest
 *
 * @package  Bluz\Tests
 *
 * @author   Taras Seryogin
 * @created  23.06.14 12.08
 */
class FileUploadTest extends Bluz\Tests\TestCase
{
    /**
     * @var string Path to test image1
     */
    protected $path1;

    /**
     * @var string Path to test image2
     */
    protected $path2;

    /**
     * SetUp
     */
    protected function setUp()
    {
        $this->path1 = Proxy\Config::getData('temp', 'image1');
        $this->path2 = Proxy\Config::getData('temp', 'image2');
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['CONTENT_LENGTH'] = 0;
    }

    /**
     * Tear Down
     *
     * @return void
     */
    protected function tearDown()
    {
        self::resetApp();
    }

    /**
     * Test FileUpload
     */
    public function testFileUpload()
    {
        $_FILES = array(
            'file' => array(
                'name' => 'test.jpeg',
                'size' => filesize($this->path1),
                'type' => 'image/jpeg',
                'tmp_name' => $this->path1,
                'error' => 0
            )
        );

        $result = Request::getFileUpload()->getFile('file');
        $this->assertNotNull($result);
    }

    /**
     * Test FileUpload with numeric sub key
     */
    public function testFilesUploadWithNumericSubKey()
    {
        $_FILES = array(
            'file' => array(
                'name' => array(
                    'test.jpeg',
                    'test1.jpeg'
                ),
                'size' => array(
                    filesize($this->path1),
                    filesize($this->path2)
                ),
                'type' => array(
                    'image/jpeg',
                    'image/jpeg'
                ),
                'tmp_name' => array(
                    $this->path1,
                    $this->path2
                ),
                'error' => array(
                    0,
                    0
                )
            )
        );

        $result = Request::getFileUpload()->getFiles('file');
        $this->assertNotEmpty($result);
    }

    /**
     * Test FileUpload with string sub key
     */
    public function testFileUploadWithStringSubKey()
    {
        $_FILES = array(
            'file' => array(
                'name' => array(
                    'a' => 'test.jpeg'
                ),
                'size' => array(
                    'a' => filesize($this->path1)
                ),
                'type' => array(
                    'a' => 'image/jpeg'
                ),
                'tmp_name' => array(
                    'a' => $this->path1
                ),
                'error' => array(
                    'a' => 0
                )
            )
        );

        $result = Request::getFileUpload()->getFile('file[a]');
        $this->assertNotNull($result);
    }

    /**
     * Test FileUpload with two sub key
     */
    public function testFileUploadWithTwoSubKey()
    {
        $_FILES = array(
            'file' => array(
                'name' => array(
                    'a' => array(
                        'b' => 'test.jpeg'
                    )
                ),
                'size' => array(
                    'a' => array(
                        'b' => filesize($this->path1)
                    )
                ),
                'type' => array(
                    'a' => array(
                        'b' => 'image/jpeg'
                    )
                ),
                'tmp_name' => array(
                    'a' => array(
                        'b' => $this->path1
                    )
                ),
                'error' => array(
                    'a' => array(
                        'b' => 0
                    )
                )
            )
        );

        $result = Request::getFileUpload()->getFile('file[a][b]');
        $this->assertNotNull($result);
    }

    /**
     * Test FileUpload with three sub key
     */
    public function testFileUploadWithThreeSubKey()
    {
        $_FILES = array(
            'file' => array(
                'name' => array(
                    'a' => array(
                        'b' => array(
                            'test.jpeg',
                            'test1.jpeg'
                        )
                    )
                ),
                'size' => array(
                    'a' => array(
                        'b' => array(
                            filesize($this->path1),
                            filesize($this->path2)
                        )
                    )
                ),
                'type' => array(
                    'a' => array(
                        'b' => array(
                            'image/jpeg',
                            'image/jpeg'
                        )
                    )
                ),
                'tmp_name' => array(
                    'a' => array(
                        'b' => array(
                            $this->path1,
                            $this->path2
                        )
                    )
                ),
                'error' => array(
                    'a' => array(
                        'b' => array(
                            0,
                            0
                        )
                    )
                )
            )
        );

        $result = Request::getFileUpload()->getFiles('file[a][b]');
        $this->assertNotEmpty($result);
    }
}
