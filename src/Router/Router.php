<?php
/**
 * Copyright (c) 2012 by Bluz PHP Team
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
namespace Bluz\Router;

/**
 * Router
 *
 * @category Bluz
 * @package  Router
 *
 * @author   Anton Shevchuk
 * @created  06.07.11 18:16
 */
class Router
{
    use \Bluz\Package;

    /**
     * Or should be as properties?
     */
    const DEFAULT_MODULE = 'index';
    const DEFAULT_CONTROLLER = 'index';
    const ERROR_MODULE = 'error';
    const ERROR_CONTROLLER = 'error';

    /**
     * @var array
     */
    protected $routers = array();

    /**
     * @var array
     */
    protected $reverse = array();

    /**
     * @var string
     */
    protected $baseUrl;

    /**
     * init
     *
     * @param array $options
     * @return void
     */
    public function init($options = null)
    {
        $routers = $this->getApplication()->getCache()->get('router:routers');
        $reverse = $this->getApplication()->getCache()->get('router:reverse');
        // FIXME: disable cache for test
//        $routers = $reverse = null;

        if (!$routers or !$reverse) {
            $routers = array();
            $reverse = array();
            foreach (new \GlobIterator(PATH_APPLICATION . '/modules/*/controllers/*.php') as $file) {
                $module = pathinfo(dirname(dirname($file->getPathname())), PATHINFO_FILENAME);
                $controller = pathinfo($file->getPathname(), PATHINFO_FILENAME);
                $data = $this->getApplication()->reflection($file->getPathname());
                if (isset($data['route'])) {
                    if (!isset($reverse[$module])) {
                        $reverse[$module] = array();
                    }

                    $reverse[$module][$controller] = ['route' => $data['route'], 'params' => $data['types']];

                    $pattern = trim($data['route']);
                    $pattern = str_replace('/', '\/', $pattern);

                    foreach ($data['types'] as $param => $type) {
                        switch ($type) {
                            case 'int':
                            case 'integer':
                                $pattern = str_replace("{\$".$param."}", "(?P<$param>[0-9]+)", $pattern);
                                break;
                            case 'float':
                                $pattern = str_replace("{\$".$param."}", "(?P<$param>[0-9.,]+)", $pattern);
                                break;
                            case 'string':
                            case 'module':
                            case 'controller':
                                $pattern = str_replace("{\$".$param."}", "(?P<$param>\w+)", $pattern);
                                break;
                        }
                    }
                    $pattern = '/^'.$pattern.'/i';
                    $routers[trim($data['route'])] = ['pattern' => $pattern, 'module' => $module, 'controller' => $controller, 'params' => $data['types']];
                }
            }
            $this->getApplication()->getCache()->set('router:routers', $routers);
            $this->getApplication()->getCache()->set('router:reverse', $reverse);
        }
        $this->routers = $routers;
        $this->reverse = $reverse;
    }

    /**
     * getBaseUrl
     * always return string with slash at end
     * @return string
     */
    public function getBaseUrl()
    {
        if (!$this->baseUrl) {
            $this->baseUrl = $this->getApplication()
                ->getRequest()
                ->getBaseUrl()
            ;
        }
        return $this->baseUrl;
    }

    /**
     * getFullUrl
     *
     * @param string $module
     * @param string $controller
     * @param array $params
     * @return string
     */
    public function getFullUrl($module = self::DEFAULT_MODULE, $controller = self::DEFAULT_CONTROLLER, $params = array())
    {
        $scheme = $this->getApplication()->getRequest()->getScheme() . '://';
        $host = $this->getApplication()->getRequest()->getHttpHost();
        $url = $this->url($module, $controller, $params);
        return $scheme . $host . $url;
    }

    /**
     * build URL
     *
     * @param string $module
     * @param string $controller
     * @param array $params
     * @return string
     */
    public function url($module = self::DEFAULT_MODULE, $controller = self::DEFAULT_CONTROLLER, $params = array())
    {
        if (empty($this->routers)) {
            return $this->urlRoute($module, $controller, $params);
        } else {
            if (isset($this->reverse[$module]) && isset($this->reverse[$module][$controller])) {
                return $this->urlCustom($module, $controller, $params);
            }
            return $this->urlRoute($module, $controller, $params);
        }
    }


    /**
     * build URL by default route
     *
     * @param string $module
     * @param string $controller
     * @param array $params
     * @return string
     */
    public function urlCustom($module, $controller, $params)
    {
        $url = $this->reverse[$module][$controller]['route'];

        $getParams = array();
        foreach ($params as $key => $value) {
            // sub-array as GET params
            if (is_array($value)) {
                $getParams[$key] = $value;
                continue;
            }
            $url = str_replace('{$'.$key.'}', $value, $url);
        }
        if (!empty($getParams)) {
            $url .= '?'. http_build_query($getParams);
        }
        return $this->getBaseUrl() . ltrim($url, '/');
    }

    /**
     * build URL by default route
     *
     * @param string $module
     * @param string $controller
     * @param array $params
     * @return string
     */
    public function urlRoute($module, $controller, $params)
    {
        $url = $this->getBaseUrl();

        if (empty($params)) {
            if ($controller == self::DEFAULT_CONTROLLER) {
                if ($module == self::DEFAULT_MODULE) {
                    return $url;
                } else {
                    return $url . $module;
                }
            }
        }

        $url .= $module.'/'.$controller;
        $getParams = array();
        foreach ($params as $key => $value) {
            // sub-array as GET params
            if (is_array($value)) {
                $getParams[$key] = $value;
                continue;
            }
            $url .= '/'.urlencode($key).'/'.urlencode($value);
        }
        if (!empty($getParams)) {
            $url .= '?'. http_build_query($getParams);
        }
        return $url;
    }

    /**
     * process
     *
     * @return \Bluz\Router\Router
     */
    public function process()
    {
        switch (true) {
            // try process default router
            case $this->processDefault():
                break;
            // try process custom routers
            case $this->processCustom():
                break;
            // try process router
            case $this->processRoute():
                break;
        }

        return $this;
    }

    /**
     * process default router
     *
     * @return boolean
     */
    protected function processDefault()
    {
        $uri = $this->getApplication()->getRequest()->getCleanUri();
        return empty($uri);
    }


    /**
     * process custom router
     *
     * @return boolean
     */
    protected function processCustom()
    {
        $request = $this->getApplication()->getRequest();
        $uri = '/'. $request->getCleanUri();

        foreach ($this->routers as $key => $router) {
            if (preg_match($router['pattern'], $uri, $matches)) {
                $request->setModule($router['module']);
                $request->setController($router['controller']);

                foreach($router['params'] as $param => $type) {
                    if (isset($matches[$param])) {
                        $request->{$param} = $matches[$param];
                    }
                }
                return true;
            }
        }
        return false;
    }

    /**
     * process default router
     *
     * @return boolean
     */
    protected function processRoute()
    {
        $request = $this->getApplication()->getRequest();
        $uri = $request->getCleanUri();
        $uri = trim($uri, '/');
        $params = preg_split('/\//', $uri);

        if (sizeof($params)) {
            $request->setModule(array_shift($params));
        }
        if (sizeof($params)) {
            $request->setController(array_shift($params));
        }
        if ($size = sizeof($params)) {
            if ($size%2==1) {
                array_pop($params);
                $size = sizeof($params);
            }
            // or use array_chunk and run another loop?
            for ($i = 0; $i < $size; $i=$i+2) {
                $request->{$params[$i]} = $params[$i+1];
            }
        }

        return true;
    }

}
