<?php

/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */

namespace Bluz\Tests\Unit;

use Bluz\Application\Application;
use Bluz\Controller\Controller;
use Bluz\Http\Exception\ForbiddenException;
use Bluz\Http\Exception\RedirectException;
use Bluz\Proxy\Layout;
use Bluz\Proxy\Request;
use Bluz\Proxy\Response;
use Exception;

/**
 * Bootstrap
 *
 * @package  Bluz\Tests
 *
 * @author   Anton Shevchuk
 * @created  20.07.11 17:38
 */
class Bootstrap extends Application
{
    /**
     * Dispatched module name
     *
     * @var string
     */
    protected string $dispatchModule;

    /**
     * Dispatched controller name
     *
     * @var string
     */
    protected string $dispatchController;

    /**
     * @var Exception
     */
    protected Exception $exception;

    /**
     * Get dispatched module name
     *
     * @return string|null
     */
    public function getModule(): ?string
    {
        return $this->dispatchModule;
    }

    /**
     * Get dispatched controller name
     *
     * @return string|null
     */
    public function getController(): ?string
    {
        return $this->dispatchController;
    }

    /**
     * setException
     *
     * @param Exception $exception
     *
     * @return void
     */
    public function setException(Exception $exception): void
    {
        $this->exception = $exception;

        codecept_debug(' ## ' . $exception->getCode());
        codecept_debug(' ## ' . $exception->getMessage());
    }

    /**
     * getException
     *
     * @return Exception|null
     */
    public function getException(): ?Exception
    {
        return $this->exception;
    }

    /**
     * Extension point: pre process
     *
     * - Router processing
     * - Analyse request headers
     *
     * @return void
     */
    protected function preProcess(): void
    {
        $this->initRouter();

        parent::preProcess();
    }

    /**
     * @inheritdoc
     */
    protected function doProcess(): void
    {
        $module = Request::getModule();
        $controller = Request::getController();
        $params = Request::getParams();

        // try to dispatch controller
        try {
            // dispatch controller
            $result = $this->dispatch($module, $controller, $params);
        } catch (ForbiddenException $e) {
            $this->setException($e);
            $result = $this->forbidden($e);
        } catch (RedirectException $e) {
            $this->setException($e);
            $result = $this->redirect($e);
        } catch (\Exception $e) {
            $this->setException($e);
            $result = $this->error($e);
        }

        if ($result instanceof Controller) {
            $this->dispatchModule = $result->getModule();
            $this->dispatchController = $result->getController();
        }

        // setup layout, if needed
        if ($this->hasLayout()) {
            // render view to layout
            // needed for headScript and headStyle helpers
            Layout::setContent($result->render());
            Response::setBody(Layout::getInstance());
        } else {
            Response::setBody($result);
        }
    }
}
