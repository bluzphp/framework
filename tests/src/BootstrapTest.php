<?php
/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Tests;

use Bluz\Application\Application;
use Bluz\Application\Exception\ForbiddenException;
use Bluz\Application\Exception\RedirectException;
use Bluz\Controller\Controller;
use Bluz\Proxy\Layout;
use Bluz\Proxy\Request;
use Bluz\Proxy\Response;

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
     *
     * @var string
     */
    protected $dispatchModule;

    /**
     * Dispatched controller name
     *
     * @var string
     */
    protected $dispatchController;

    /**
     * @var \Exception
     */
    protected $exception;

    /**
     * Get dispatched module name
     *
     * @return string|null
     */
    public function getModule()
    {
        return $this->dispatchModule;
    }

    /**
     * Get dispatched controller name
     *
     * @return string|null
     */
    public function getController()
    {
        return $this->dispatchController;
    }

    /**
     * setException
     *
     * @param \Exception $exception
     *
     * @return void
     */
    public function setException($exception)
    {
        $this->exception = $exception;

        codecept_debug(' ## '. $exception->getCode());
        codecept_debug(' ## '. $exception->getMessage());
    }

    /**
     * getException
     *
     * @return \Exception|null
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * @inheritdoc
     */
    protected function doProcess()
    {
        $module = Request::getModule();
        $controller = Request::getController();
        $params = Request::getParams();

        // try to dispatch controller
        try {
            codecept_debug('');
            codecept_debug(' >> '. $module .'/'. $controller);

            // dispatch controller
            $result = $this->dispatch($module, $controller, $params);
        } catch (ForbiddenException $e) {
            $this->setException($e);
            $result = $this->forbidden($e);
        } catch (RedirectException $e) {
            $this->setException($e);
            // redirect to URL
            $result = $this->redirect($e->getUrl());
        } catch (\Exception $e) {
            $this->setException($e);
            $result = $this->error($e);
        }

        if ($result instanceof Controller) {
            $this->dispatchModule = $result->getModule();
            $this->dispatchController = $result->getController();
            codecept_debug(' << '. $this->getModule() .'/'. $this->getController());
        }

        // setup layout, if needed
        if ($this->useLayout()) {
            // render view to layout
            // needed for headScript and headStyle helpers
            Layout::setContent($result->render());
            Response::setBody(Layout::getInstance());
        } else {
            Response::setBody($result);
        }
    }
}
