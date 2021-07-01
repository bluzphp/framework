<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Controller;

use Bluz\Application\Application;
use Bluz\Auth\IdentityInterface;
use Bluz\Common\Exception\CommonException;
use Bluz\Common\Exception\ComponentException;
use Bluz\Common\Helper;
use Bluz\Http\Exception\ForbiddenException;
use Bluz\Proxy\Cache;
use Bluz\Proxy\Logger;
use Bluz\Response\ResponseTrait;
use Bluz\View\View;
use Closure;
use Exception;
use JsonSerializable;
use ReflectionException;

/**
 * Statement
 *
 * @package  Bluz\Controller
 * @author   Anton Shevchuk
 *
 * @method void attachment(string $file)
 * @method void checkHttpAccept()
 * @method void checkHttpMethod()
 * @method void checkPrivilege()
 * @method void denied()
 * @method void disableLayout()
 * @method void disableView()
 * @method Controller dispatch(string $module, string $controller, array $params = [])
 * @method void redirect(string $url)
 * @method void redirectTo(string $module, string $controller, array $params = [])
 * @method void reload()
 * @method bool isAllowed($privilege)
 * @method void useJson()
 * @method void useLayout($layout)
 * @method IdentityInterface user()
 */
class Controller implements JsonSerializable
{
    use Helper;
    use ResponseTrait;

    /**
     * @var string
     */
    protected $module;

    /**
     * @var string
     */
    protected $controller;

    /**
     * @var array
     */
    protected $params;

    /**
     * @var string Cache key
     */
    protected $key;

    /**
     * @var string Template name, by default is equal to controller name
     */
    protected $template;

    /**
     * @var string
     */
    protected $file;

    /**
     * @var Meta
     */
    protected $meta;

    /**
     * @var Data
     */
    protected $data;

    /**
     * Constructor of Statement
     *
     * @param string $module
     * @param string $controller
     * @param array  $params
     *
     * @throws CommonException
     */
    public function __construct($module, $controller, array $params = [])
    {
        // initial default helper path
        $this->addHelperPath(__DIR__ . '/Helper/');

        $this->setModule($module);
        $this->setController($controller);
        $this->setParams($params);
        $this->setTemplate($controller . '.phtml');

        $this->key = "data.$module.$controller." . md5(http_build_query($params));
    }

    /**
     * @return string
     */
    public function getModule(): string
    {
        return $this->module;
    }

    /**
     * @param string $module
     */
    protected function setModule(string $module): void
    {
        $this->module = $module;
    }

    /**
     * @return string
     */
    public function getController(): string
    {
        return $this->controller;
    }

    /**
     * @param string $controller
     */
    protected function setController(string $controller): void
    {
        $this->controller = $controller;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @param array $params
     */
    protected function setParams(array $params): void
    {
        $this->params = $params;
    }

    /**
     * @return string
     */
    public function getTemplate(): ?string
    {
        return $this->template;
    }

    /**
     * @param string $template
     */
    protected function setTemplate(string $template): void
    {
        $this->template = $template;
    }

    /**
     * Run controller logic
     *
     * @return Data
     * @throws ComponentException
     * @throws ControllerException
     * @throws ReflectionException
     */
    public function run(): Data
    {
        if (!$this->loadData()) {
            $this->process();
            $this->saveData();
        }
        return $this->data;
    }

    /**
     * Controller run
     *
     * @return Data
     * @throws ComponentException
     * @throws ControllerException
     * @throws ReflectionException
     */
    protected function process(): Data
    {
        // initial variables for use inside controller
        $module = $this->module;
        $controller = $this->controller;
        $params = $this->params;

        /**
         * @var Closure $controllerClosure
         */
        $controllerClosure = include $this->getFile();

        if (!is_callable($controllerClosure)) {
            throw new ControllerException("Controller is not callable '{$module}/{$controller}'");
        }

        // process params
        $params = $this->getMeta()->params($params);

        // call Closure or Controller
        $result = $controllerClosure(...$params);

        // switch statement for result of Closure run
        switch (true) {
            case ($result === false):
                // return "false" is equal to disable view and layout
                $this->disableLayout();
                $this->disableView();
                break;
            case is_string($result):
                // return string variable is equal to change view template
                $this->setTemplate($result);
                break;
            case is_array($result):
                // return associative array is equal to setup view data
                $this->getData()->setFromArray($result);
                break;
            case ($result instanceof self):
                // return Controller - just extract data from it
                $this->getData()->setFromArray($result->getData()->toArray());
                break;
        }

        return $this->getData();
    }

    /**
     * Setup controller file
     *
     * @return void
     * @throws ControllerException
     * @throws ReflectionException
     */
    protected function findFile(): void
    {
        $path = Application::getInstance()->getPath();
        $file = "$path/modules/{$this->module}/controllers/{$this->controller}.php";

        if (!file_exists($file)) {
            throw new ControllerException("Controller file not found '{$this->module}/{$this->controller}'", 404);
        }

        $this->file = $file;
    }

    /**
     * Get controller file path
     *
     * @return string
     * @throws ControllerException
     * @throws ReflectionException
     */
    protected function getFile(): string
    {
        if (!$this->file) {
            $this->findFile();
        }
        return $this->file;
    }

    /**
     * Retrieve reflection for anonymous function
     *
     * @return void
     * @throws ComponentException
     * @throws ControllerException
     * @throws ReflectionException
     */
    protected function initMeta(): void
    {
        // cache for reflection data
        $cacheKey = "meta.{$this->module}.{$this->controller}";

        if (!$meta = Cache::get($cacheKey)) {
            $meta = new Meta($this->getFile());
            $meta->process();

            Cache::set(
                $cacheKey,
                $meta,
                Cache::TTL_NO_EXPIRY,
                ['system', 'meta']
            );
        }
        $this->meta = $meta;
    }

    /**
     * Get meta information
     *
     * @return Meta
     * @throws ControllerException
     * @throws ComponentException
     * @throws ReflectionException
     */
    public function getMeta(): Meta
    {
        if (!$this->meta) {
            $this->initMeta();
        }
        return $this->meta;
    }

    /**
     * Assign key/value pair to Data object
     *
     * @param  string $key
     * @param  mixed  $value
     *
     * @return void
     */
    public function assign($key, $value): void
    {
        $this->getData()->set($key, $value);
    }

    /**
     * Get controller Data container
     *
     * @return Data
     */
    public function getData(): Data
    {
        if (!$this->data) {
            $this->data = new Data();
        }
        return $this->data;
    }

    /**
     * Load Data from cache
     *
     * @return bool
     * @throws ComponentException
     * @throws ControllerException
     * @throws ReflectionException
     */
    private function loadData(): bool
    {
        $cacheTime = $this->getMeta()->getCache();

        if ($cacheTime && $cached = Cache::get($this->key)) {
            $this->data = $cached;
            return true;
        }
        return false;
    }

    /**
     * Save Data to cache
     *
     * @return bool
     * @throws ComponentException
     * @throws ControllerException
     * @throws ReflectionException
     */
    private function saveData(): bool
    {
        if ($cacheTime = $this->getMeta()->getCache()) {
            return Cache::set(
                $this->key,
                $this->getData(),
                $cacheTime,
                ['system', 'data']
            );
        }
        return false;
    }

    /**
     * Specify data which should be serialized to JSON
     *
     * @return Data
     */
    public function jsonSerialize()
    {
        return $this->getData();
    }

    /**
     * Magic cast to string
     *
     * @return string
     */
    public function __toString()
    {
        if (!$this->template) {
            return '';
        }

        try {
            // $view for use in closure
            $view = new View();

            $path = Application::getInstance()->getPath();

            // setup additional helper path
            $view->addHelperPath($path . '/layouts/helpers');

            // setup additional partial path
            $view->addPartialPath($path . '/layouts/partial');

            // setup default path
            $view->setPath($path . '/modules/' . $this->module . '/views');

            // setup template
            $view->setTemplate($this->template);

            // setup data
            $view->setFromArray($this->getData()->toArray());
            return $view->render();
        } catch (Exception $e) {
            // save log
            Logger::exception($e);
            return '';
        }
    }
}
