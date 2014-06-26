<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Http;

use Bluz\Request\RequestException;

/**
 * HttpFileUpload
 *
 * @package  Bluz\Request
 *
 * @author   Anton Shevchuk
 * @created  07.02.13 13:20
 */
class FileUpload
{
    /**
     * @var array of Files
     */
    protected $files = array();

    /**
     * __construct
     *
     * @param array $array The array of $_FILES
     * @throws RequestException
     */
    public function __construct($array = null)
    {
        // check max file size error
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && empty($_POST) &&
            empty($_FILES) && $_SERVER['CONTENT_LENGTH'] > 0 ) {
            $displayMaxSize = ini_get('post_max_size');

            $error = 'Posted file is too large. '. $_SERVER["CONTENT_LENGTH"].
                ' bytes exceeds the maximum size of '. $displayMaxSize;
            // mute error message by user notice
            @trigger_error($error, E_USER_NOTICE);
            throw new RequestException($error);
        }

        $rawFiles = $array ? : $_FILES;
        foreach ($rawFiles as $key => $file) {
            $this->processFileArray($key, $file);
        }
    }

    /*
        name=data
        data
        name => value
        type => value

        name=data[]
        data
        name => 0 => value
                1 => value
        type => 0 => value
                1 => value

        name=data[a]
        data
        name => a => value
        type => a => value

        name=data[a][b]
        data
        name => a => b => value
        type => a => b => value

        name=data[a][b][]
        data
        name => a => b => 0 => value
                          1 => value
        type => a => b => 0 => value
                          1 => value
    */

    /**
     * process file information from array
     * FIXME: this hell
     *
     * @param $key
     * @param $fileInfo
     * @return array
     */
    protected function processFileArray($key, $fileInfo)
    {
        if (is_array($fileInfo['name'])) {
            foreach ($fileInfo['name'] as $subKey => $name) {
                if (is_numeric($subKey)) {
                    $this->createFile($fileInfo, $key);
                } elseif (is_array($fileInfo['name'][$subKey])) {
                    foreach ($fileInfo['name'][$subKey] as $subSubKey => $subName) {
                        if (is_array($fileInfo['name'][$subKey][$subSubKey])) {
                            foreach ($fileInfo['name'][$subKey][$subSubKey] as $subSubSubKey => $subSubName) {
                                $this->createFile($fileInfo, $key, $subKey, $subSubKey);
                            }
                        } else {
                            $this->createFile($fileInfo, $key, $subKey, $subSubKey);
                        }
                    }
                } else {
                    $this->createFile($fileInfo, $key, $subKey);
                }
            }
        } else {
            $this->createFile($fileInfo, $key);
        }
    }

    /**
     * createFile
     *
     * @param array $fileInfo
     * @param string $fileKey
     * @param string $subKey
     * @param string $subSubKey
     * @param string $subSubSubKey
     *
     * @return File instance
     */
    public function createFile($fileInfo, $fileKey, $subKey = null, $subSubKey = null, $subSubSubKey = null)
    {
        $argList = func_get_args();
        $fileInfo = array_shift($argList);
        $fileKey = array_shift($argList);

        foreach ($fileInfo as $key => $value) {
            $fileInfo[$key] = $this->parseArray($value, $argList);
        }

        if (count($argList)) {
            $fileKey .= '[' . implode("][", $argList) . ']';
        }

        $this->files[$fileKey][] = new File($fileInfo);
    }

    /**
     * parseArray
     *
     * @param array|string $nameArr
     * @param array|null $argList
     *
     * @return string
     */
    public function parseArray($nameArr, $argList)
    {
        if (!is_array($nameArr)) {
            return $nameArr;
        }

        $subKey = current($argList);
        array_shift($argList);
        return $this->parseArray($nameArr[$subKey], $argList);
    }

    /**
     * getFile
     *
     * @param string $name
     * @return File
     */
    public function getFile($name)
    {
        if (isset($this->files[$name])) {
            return current($this->files[$name]);
        } else {
            return null;
        }
    }

    /**
     * getFiles
     *
     * @param string $name
     * @return array
     */
    public function getFiles($name)
    {
        if (isset($this->files[$name])) {
            return $this->files[$name];
        } else {
            return array();
        }
    }
}
