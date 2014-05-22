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
use Bluz\Http;
use Bluz\Request\AbstractRequest;
use Bluz\Response\AbstractResponse;

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
     * @var \Application\Tests\BootstrapTest
     */
    protected $app;

    /**
     * Get Application instance
     *
     * @return BootstrapTest
     */
    protected function getApp()
    {
        if (!$this->app) {
            $env = getenv('BLUZ_ENV') ?: 'testing';

            $this->app = BootstrapTest::getInstance();
            $this->app->init($env);
        }

        return $this->app;
    }

    /**
     * Reset layout and Request
     */
    protected function resetApp()
    {
        $this->app->resetLayout();

        $this->app->getAuth()->clearIdentity();
        $this->app->setRequest(new Http\Request());
        $this->app->setResponse(new Http\Response());
        $this->app->useJson(false);
        $this->app->useLayout(true);
        $this->app->getMessages()->popAll();
    }
}
