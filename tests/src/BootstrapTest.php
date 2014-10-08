<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Tests;

use Bluz\Application\Application;

/**
 * Bootstrap
 *
 * @package  Bluz\Tests
 *
 * @author   Anton Shevchuk
 * @created  20.07.11 17:38
 */
class BootstrapTest extends Application
{
    /**
     * Dispatched module name
     * @var string
     */
    protected $dispatchModule;

    /**
     * Dispatched controller name
     * @var string
     */
    protected $dispatchController;

    /**
     * Get dispatched module name
     *
     * @return string
     */
    public function getModule()
    {
        return $this->dispatchModule;
    }

    /**
     * Get dispatched controller name
     *
     * @return string
     */
    public function getController()
    {
        return $this->dispatchController;
    }

    /**
     * @param string $module
     * @param string $controller
     * @param array $params
     * @return \Bluz\View\View|string
     */
    public function dispatch($module, $controller, $params = array())
    {
        $this->dispatchModule = $module;
        $this->dispatchController = $controller;

        return parent::dispatch($module, $controller, $params);
    }
}
