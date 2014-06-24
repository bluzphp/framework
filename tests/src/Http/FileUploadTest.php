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
     * setUp
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();
        $this->path1 = $this->getApp()->getConfigData('tmp_name', 'image1');
        $this->path2 = $this->getApp()->getConfigData('tmp_name', 'image2');
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
        parent::tearDown();
        $this->resetApp();
    }

    /**
     * Test FileUpload
     */
    public function testFileUpload()
    {
        $_FILES = array(
            'file' => array(
                'name' => 'test.jpg',
                'size' => filesize($this->path1),
                'type' => 'image/jpeg',
                'tmp_name' => $this->path1,
                'error' => 0
            )
        );

        $request = $this->getApp()->getRequest();
        $result = $request->getFileUpload()->getFile('file');
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
                    'test.jpg',
                    'test1.jpg'
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

        $request = $this->getApp()->getRequest();
        $result = $request->getFileUpload()->getFiles('file');
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
                    'a' => 'test.jpg'
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

        $request = $this->getApp()->getRequest();
        $result = $request->getFileUpload()->getFile('file[a]');
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
                        'b' => 'test.jpg'
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

        $request = $this->getApp()->getRequest();
        $result = $request->getFileUpload()->getFile('file[a][b]');
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
                            'test.jpg',
                            'test1.jpg'
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

        $request = $this->getApp()->getRequest();
        $result = $request->getFileUpload()->getFiles('file[a][b]');
        $this->assertNotEmpty($result);
    }

    /**
     * Test create file
     */
    public function testCreateFile()
    {
        $name = 'test.jpg';
        $error = 0;
        $tmpName = $this->path1;
        $type = 'image/jpeg';
        $size = filesize($this->path1);

        $request = $this->getApp()->getRequest();
        $result = $request->getFileUpload()->createFile($name, $error, $tmpName, $type, $size);
        $this->assertInstanceOf('Bluz\Http\File', $result);
    }
}
