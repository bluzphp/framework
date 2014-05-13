<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Tests;

use Bluz;

/**
 * ControllerTestCase
 *
 * @category Bluz
 * @package  Tests
 *
 * @author   Anton Shevchuk
 * @created  04.08.11 20:01
 */
class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * Application entity
     *
     * @var BootstrapTest
     */
    private $app;

    /**
     * getApp
     *
     * @return BootstrapTest
     */
    protected function getApp()
    {
        if (!$this->app) {
            $this->app = BootstrapTest::getInstance();
            $this->app->init('testing');
        }
        return $this->app;
    }
}
