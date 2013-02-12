<?php
/**
 * Copyright (c) 2013 by Bluz PHP Team
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
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
     */
    public function __construct($array = null)
    {
        $rawFiles = $array?:$_FILES;
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
                    $httpFile = new HttpFile([
                        'name' => $fileInfo['name'][$subKey],
                        'error' => $fileInfo['error'][$subKey],
                        'tmp_name' => $fileInfo['tmp_name'][$subKey],
                        'type' => $fileInfo['type'][$subKey],
                        'size' => $fileInfo['size'][$subKey],
                    ]);
                    $this->files[$key][] = $httpFile;
                } elseif (is_array($fileInfo['name'][$subKey])) {
                    foreach ($fileInfo['name'][$subKey] as $subSubKey => $subName) {
                        if (is_array($fileInfo['name'][$subKey][$subSubKey])) {
                            foreach ($fileInfo['name'][$subKey][$subSubKey] as $subSubSubKey => $subSubName) {
                                $httpFile = new HttpFile([
                                    'name' => $fileInfo['name'][$subKey][$subSubKey][$subSubSubKey],
                                    'error' => $fileInfo['error'][$subKey][$subSubKey][$subSubSubKey],
                                    'tmp_name' => $fileInfo['tmp_name'][$subKey][$subSubKey][$subSubSubKey],
                                    'type' => $fileInfo['type'][$subKey][$subSubKey][$subSubSubKey],
                                    'size' => $fileInfo['size'][$subKey][$subSubKey][$subSubSubKey],
                                ]);
                                $this->files[$key.'['.$subKey.']['.$subSubKey.']'][] = $httpFile;
                            }
                        } else {
                            $httpFile = new HttpFile([
                                'name' => $fileInfo['name'][$subKey][$subSubKey],
                                'error' => $fileInfo['error'][$subKey][$subSubKey],
                                'tmp_name' => $fileInfo['tmp_name'][$subKey][$subSubKey],
                                'type' => $fileInfo['type'][$subKey][$subSubKey],
                                'size' => $fileInfo['size'][$subKey][$subSubKey],
                            ]);
                            $this->files[$key.'['.$subKey.']'][] = $httpFile;
                        }
                    }
                } else {
                    $httpFile = new HttpFile([
                        'name' => $fileInfo['name'][$subKey],
                        'error' => $fileInfo['error'][$subKey],
                        'tmp_name' => $fileInfo['tmp_name'][$subKey],
                        'type' => $fileInfo['type'][$subKey],
                        'size' => $fileInfo['size'][$subKey],
                    ]);
                    $this->files[$key.'['.$subKey.']'][] = $httpFile;
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
