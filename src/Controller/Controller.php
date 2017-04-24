<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Controller;

use Bluz\Application\Application;
use Bluz\Application\Exception\ForbiddenException;
use Bluz\Application\Exception\NotAcceptableException;
use Bluz\Application\Exception\NotAllowedException;
use Bluz\Auth\EntityInterface;
use Bluz\Common\Helper;
use Bluz\Proxy\Acl;
use Bluz\Proxy\Cache;
use Bluz\Proxy\Logger;
use Bluz\Proxy\Request;
use Bluz\Response\ResponseTrait;
use Bluz\View\View;

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
 * @method void useJson()
 * @method void useLayout($layout)
 * @method EntityInterface user()
 */
class Controller implements \JsonSerializable
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
     * @var string
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
     * One of HTML, JSON or empty string
     * @var string
     */
    protected $render = 'HTML';

    /**
     * Constructor of Statement
     *
     * @param string $module
     * @param string $controller
     */
    public function __construct($module, $controller)
    {
        $this->module = $module;
        $this->controller = $controller;
        $this->template = $controller . '.phtml';

        // initial default helper path
        $this->addHelperPath(__DIR__ . '/Helper/');
    }

    /**
     * Check `Privilege`
     *
     * @throws ForbiddenException
     */
    public function checkPrivilege()
    {
        $privilege = $this->getMeta()->getPrivilege();
        if ($privilege && !Acl::isAllowed($this->module, $privilege)) {
            throw new ForbiddenException;
        }
    }

    /**
     * Check `Method`
     *
     * @throws NotAllowedException
     */
    public function checkMethod()
    {
        $methods = $this->getMeta()->getMethod();
        if ($methods && !in_array(Request::getMethod(), $methods)) {
            throw new NotAllowedException(implode(',', $methods));
        }
    }

    /**
     * Check `Accept`
     *
     * @throws NotAcceptableException
     */
    public function checkAccept()
    {
        // all ok for CLI
        if (PHP_SAPI === 'cli') {
            return;
        }

        $allowAccept = $this->getMeta()->getAccept();

        // some controllers hasn't @accept tag
        if (!$allowAccept) {
            // but by default allow just HTML output
            $allowAccept = [Request::TYPE_HTML, Request::TYPE_ANY];
        }

        // get Accept with high priority
        $accept = Request::getAccept($allowAccept);

        // some controllers allow any type (*/*)
        // and client doesn't send Accept header
        if (in_array(Request::TYPE_ANY, $allowAccept) && !$accept) {
            // all OK, controller should realize logic for response
            return;
        }

        // some controllers allow just selected types
        // choose MIME type by browser accept header
        // filtered by controller @accept
        // switch statement for this logic
        switch ($accept) {
            case Request::TYPE_ANY:
            case Request::TYPE_HTML:
                // HTML response with layout
                break;
            case Request::TYPE_JSON:
                // JSON response
                $this->template = null;
                break;
            default:
                throw new NotAcceptableException;
        }
    }

    /**
     * __invoke
     *
     * @param array $params
     * @return Data
     * @throws ControllerException
     */
    public function run(array $params = [])
    {
        // initial variables for use inside controller
        $module = $this->module;
        $controller = $this->controller;

        $cacheKey = "data.$module.$controller." . md5(http_build_query($params));

        $cacheTime = $this->getMeta()->getCache();

        if ($cacheTime && $cached = Cache::get($cacheKey)) {
            $this->data = $cached;
            return $cached;
        }

        /**
         * @var \closure $controllerClosure
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
                $this->template = $result;
                break;
            case is_array($result):
                // return associative array is equal to setup view data
                $this->getData()->setFromArray($result);
                break;
            case ($result instanceof Controller):
                $this->getData()->setFromArray($result->getData()->toArray());
                break;
        }

        if ($cacheTime) {
            Cache::set(
                $cacheKey,
                $this->getData(),
                $cacheTime,
                ['system', 'data', Cache::prepare("$module.$controller")]
            );
        }

        return $this->getData();
    }

    /**
     * Setup controller file
     *
     * @return void
     * @throws ControllerException
     */
    protected function setFile()
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
     * @return string
     */
    protected function getFile() // : string
    {
        if (!$this->file) {
            $this->setFile();
        }
        return $this->file;
    }

    /**
     * Retrieve reflection for anonymous function
     * @return void
     * @throws \Bluz\Common\Exception\ComponentException
     */
    protected function setMeta()
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
                ['system', 'meta', Cache::prepare($this->module . '.' . $this->controller)]
            );
        }
        $this->meta = $meta;
    }
    
    /**
     * Get meta information
     * @return Meta
     */
    public function getMeta()
    {
        if (!$this->meta) {
            $this->setMeta();
        }
        return $this->meta;
    }

    /**
     * Assign key/value pair to Data object
     * @param  string $key
     * @param  mixed  $value
     * @return Controller
     */
    public function assign($key, $value)
    {
        $this->getData()->set($key, $value);
        return $this;
    }
    
    /**
     * Get controller Data container
     *
     * @return Data
     */
    public function getData()
    {
        if (!$this->data) {
            $this->data = new Data();
        }
        return $this->data;
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
        } catch (\Exception $e) {
            // save log
            Logger::error($e->getMessage());
            return '';
        }
    }
}
