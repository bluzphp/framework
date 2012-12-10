<?php
/**
 * ControllerTestCase
 *
 * @category Tests
 * @package  Application
 *
 * @author   Anton Shevchuk
 * @created  04.08.11 20:01
 */
namespace Bluz\Tests;

use Bluz;

class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * Application entity
     *
     * @var Bluz\Bootstrap
     */
    protected $app;

    /**
     * Setup TestCase
     */
    protected function setUp()
    {

//        $this->app = BootstrapTest::getInstance();
//        $this->app->init(APPLICATION_ENV);
    }
}
