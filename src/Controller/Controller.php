<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Controller;

use Bluz\Auth\IdentityInterface;
use Bluz\Common\Exception\CommonException;
use Bluz\Common\Exception\ComponentException;
use Bluz\Common\Helper;
use Bluz\Proxy\Application;
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
    protected string $path;

    /**
     * @var string
     */
    protected string $module;

    /**
     * @var string
     */
    protected string $controller;

    /**
     * @var array
     */
    protected array $params;

    /**
     * @var array
     */
    protected array $types = [];

    /**
     * @var string|null Template name, by default is equal to controller name
     */
    protected ?string $template;

    /**
     * @var string|null
     */
    protected ?string $file;

    /**
     * @var Data|null
     */
    protected ?Data $data = null;

    /**
     * Constructor of Statement
     *
     * @param string $path
     * @param string $module
     * @param string $controller
     * @param array $params
     *
     * @throws CommonException
     * @throws ControllerException
     */
    public function __construct(string $path, string $module, string $controller, array $params = [])
    {
        // initial default helper path
        $this->addHelperPath(__DIR__ . '/Helper/');

        $this->path = $path;
        $this->module = $module;
        $this->controller = $controller;
        $this->params = $params;

        $this->setTemplate($controller . '.phtml');
        $this->findFile();
    }

    /**
     * @return string
     */
    public function getModule(): string
    {
        return $this->module;
    }

    /**
     * @return string
     */
    public function getController(): string
    {
        return $this->controller;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @return array
     */
    public function getTypes(): array
    {
        return $this->types;
    }

    /**
     * @param array $types
     */
    public function setTypes(array $types): void
    {
        $this->types = $types;
    }

    /**
     * @return string|null
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
     * Controller run
     *
     * @return Data
     * @throws ComponentException
     * @throws ControllerException
     * @throws ReflectionException
     */
    public function process(): Data
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
        $params = $this->params();

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
     * Process request params
     *
     *  - type conversion
     *  - set default value
     *
     * @return array
     */
    protected function params(): array
    {
        // apply type and default value for request params
        $params = [];
        foreach ($this->types as $param => $type) {
            if (isset($this->params[$param])) {
                $params[] = match ($type) {
                    'bool' => (bool)$this->params[$param],
                    'int' => (int)$this->params[$param],
                    'float' => (float)$this->params[$param],
                    'string' => (string)$this->params[$param],
                    'array' => (array)$this->params[$param],
                    default => $this->params[$param],
                };
            }
        }
        return $params;
    }


    /**
     * Setup controller file
     *
     * @return void
     * @throws ControllerException
     */
    protected function findFile(): void
    {
        $file = "{$this->path}/modules/{$this->module}/controllers/{$this->controller}.php";

        if (!file_exists($file)) {
            throw new ControllerException("Controller file not found '{$this->module}/{$this->controller}'", 404);
        }

        $this->file = $file;
    }

    /**
     * Get controller file path
     *
     * @return string
     */
    protected function getFile(): string
    {
        return $this->file;
    }

    /**
     * Assign key/value pair to Data object
     *
     * @param string $key
     * @param mixed $value
     *
     * @return void
     */
    public function assign(string $key, mixed $value): void
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
     * Set controller Data container
     *
     * @param Data $data
     * @return void
     */
    public function setData(Data $data): void
    {
        $this->data = $data;
    }

    /**
     * Specify data which should be serialized to JSON
     *
     * @return Data
     */
    public function jsonSerialize(): mixed
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

            // setup additional helper path
            $view->addHelperPath($this->path . '/layouts/helpers');

            // setup additional partial path
            $view->addPartialPath($this->path . '/layouts/partial');

            // setup default path
            $view->setPath($this->path . '/modules/' . $this->module . '/views');

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
