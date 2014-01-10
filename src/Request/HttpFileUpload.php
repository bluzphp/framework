<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Request;

/**
 * HttpFileUpload
 *
 * @category Bluz
 * @package  Request
 *
 * @author   Anton Shevchuk
 * @created  07.02.13 13:20
 */
class HttpFileUpload
{
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
                    $httpFile = new HttpFile(
                        [
                            'name' => $fileInfo['name'][$subKey],
                            'error' => $fileInfo['error'][$subKey],
                            'tmp_name' => $fileInfo['tmp_name'][$subKey],
                            'type' => $fileInfo['type'][$subKey],
                            'size' => $fileInfo['size'][$subKey],
                        ]
                    );
                    $this->files[$key][] = $httpFile;
                } elseif (is_array($fileInfo['name'][$subKey])) {
                    foreach ($fileInfo['name'][$subKey] as $subSubKey => $subName) {
                        if (is_array($fileInfo['name'][$subKey][$subSubKey])) {
                            foreach ($fileInfo['name'][$subKey][$subSubKey] as $subSubSubKey => $subSubName) {
                                $httpFile = new HttpFile(
                                    [
                                        'name' => $fileInfo['name'][$subKey][$subSubKey][$subSubSubKey],
                                        'error' => $fileInfo['error'][$subKey][$subSubKey][$subSubSubKey],
                                        'tmp_name' => $fileInfo['tmp_name'][$subKey][$subSubKey][$subSubSubKey],
                                        'type' => $fileInfo['type'][$subKey][$subSubKey][$subSubSubKey],
                                        'size' => $fileInfo['size'][$subKey][$subSubKey][$subSubSubKey],
                                    ]
                                );
                                $this->files[$key . '[' . $subKey . '][' . $subSubKey . ']'][] = $httpFile;
                            }
                        } else {
                            $httpFile = new HttpFile(
                                [
                                    'name' => $fileInfo['name'][$subKey][$subSubKey],
                                    'error' => $fileInfo['error'][$subKey][$subSubKey],
                                    'tmp_name' => $fileInfo['tmp_name'][$subKey][$subSubKey],
                                    'type' => $fileInfo['type'][$subKey][$subSubKey],
                                    'size' => $fileInfo['size'][$subKey][$subSubKey],
                                ]
                            );
                            $this->files[$key . '[' . $subKey . ']'][] = $httpFile;
                        }
                    }
                } else {
                    $httpFile = new HttpFile(
                        [
                            'name' => $fileInfo['name'][$subKey],
                            'error' => $fileInfo['error'][$subKey],
                            'tmp_name' => $fileInfo['tmp_name'][$subKey],
                            'type' => $fileInfo['type'][$subKey],
                            'size' => $fileInfo['size'][$subKey],
                        ]
                    );
                    $this->files[$key . '[' . $subKey . ']'][] = $httpFile;
                }
            }
        } else {
            $httpFile = new HttpFile($fileInfo);
            $this->files[$key] = [$httpFile];
        }
    }

    /**
     * getFile
     *
     * @param string $name
     * @return HttpFile
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
