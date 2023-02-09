<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Application;

use Bluz\Common\Exception\CommonException;
use Bluz\Common\Exception\ComponentException;
use Bluz\Common\Exception\ConfigurationException;
use Bluz\Common\Helper;
use Bluz\Common\Nil;
use Bluz\Config\Config;
use Bluz\Config\ConfigException;
use Bluz\Config\ConfigLoader;
use Bluz\Controller\Controller;
use Bluz\Controller\ControllerException;
use Bluz\Http\Exception\ForbiddenException;
use Bluz\Http\Exception\NotAcceptableException;
use Bluz\Http\Exception\NotAllowedException;
use Bluz\Http\Exception\RedirectException;
use Bluz\Http\MimeType;
use Bluz\Logger\Logger;
use Bluz\Messages\Messages;
use Bluz\Proxy\Acl as AclProxy;
use Bluz\Proxy\Application as ApplicationProxy;
use Bluz\Proxy\Cache as CacheProxy;
use Bluz\Proxy\Config as ConfigProxy;
use Bluz\Proxy\Layout as LayoutProxy;
use Bluz\Proxy\Logger as LoggerProxy;
use Bluz\Proxy\Messages as MessagesProxy;
use Bluz\Proxy\Request as RequestProxy;
use Bluz\Proxy\Response as ResponseProxy;
use Bluz\Proxy\Router as RouterProxy;
use Bluz\Proxy\Session as SessionProxy;
use Bluz\Proxy\Translator as TranslatorProxy;
use Bluz\Request\Request;
use Bluz\Response\ResponseType;
use Bluz\Response\Response;
use Bluz\Router\Router;
use Bluz\Session\Session;
use Bluz\Translator\Translator;
use Exception;
use Psr\Cache\CacheException;
use ReflectionException;

/**
 * Application
 *
 * @package  Bluz\Application
 * @link     https://github.com/bluzphp/framework/wiki/Application
 *
 * @author   Anton Shevchuk
 *
 * @method Controller error(Exception $exception)
 * @method mixed forbidden(ForbiddenException $exception)
 * @method null redirect(RedirectException $url)
 */
class Application
{
    use Helper;

    /**
     * @var bool Debug application flag
     */
    protected bool $debugFlag = false;

    /**
     * @var bool Layout usage flag
     */
    protected bool $layoutFlag = true;

    /**
     * @var Data of the Application, cached!
     */
    private Data $data;

    /**
     * @param string $path
     * @param string $baseUrl
     * @param string $environment
     * @throws CommonException
     */
    public function __construct(
        protected string $path,
        protected string $baseUrl = '/',
        protected string $environment = 'production'
    ) {
        // initial default helper path
        $this->addHelperPath(__DIR__ . '/Helper/');

        // setup singleton instance
        ApplicationProxy::setInstance($this);
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * @return string
     */
    public function getEnvironment(): string
    {
        return $this->environment;
    }

    /**
     * Return Application data
     *
     * @return Data
     */
    public function getData(): Data
    {
        return $this->data;
    }

    /**
     * Return Debug flag
     *
     * @return bool
     */
    public function isDebug(): bool
    {
        return $this->debugFlag;
    }

    /**
     * Return Layout flag
     *
     * @return bool
     */
    public function hasLayout(): bool
    {
        return $this->layoutFlag;
    }

    /**
     * @return void
     */
    public function enableLayout(): void
    {
        $this->layoutFlag = true;
    }

    /**
     * @return void
     */
    public function disableLayout(): void
    {
        $this->layoutFlag = false;
    }

    /**
     * Initialize system packages
     *
     * @return void
     * @throws ApplicationException
     */
    public function init(): void
    {
        try {
            $this->initConfig();
            $this->initLogger();

            {
                $this->applyConfig();
            }

            $this->initCache();
            $this->initData();
            $this->initSession();
            $this->initMessages();
            $this->initRequest();
            $this->initResponse();
            $this->initRouter();
            $this->initTranslator();
        } catch (\Throwable $e) {
            throw new ApplicationException("Application can't be loaded: " . $e->getMessage());
        }
    }

    /**
     * Initial Config Proxy
     *
     * @return void
     * @throws ConfigException
     */
    protected function initConfig(): void
    {
        // load and merge configs
        $loader = new ConfigLoader();
        $loader->load($this->getPath() . '/configs/default');
        $loader->load($this->getPath() . '/configs/' . $this->environment);

        // initial Config instance
        $config = new Config();
        $config->setFromArray($loader->getConfig());

        ConfigProxy::setInstance($config);
    }

    /**
     * Initial Logger Proxy
     *
     * @return void
     */
    protected function initLogger(): void
    {
        if (ConfigProxy::get('logger')) {
            $logger = new Logger();
        } else {
            $logger = new Nil();
        }

        LoggerProxy::setInstance($logger);
    }

    /**
     * Apply Config settings
     *
     * @return void
     */
    protected function applyConfig(): void
    {
        // setup debug option
        $this->debugFlag = ConfigProxy::get('debug');
        LoggerProxy::info('app:init:debug:' . $this->debugFlag);

        // setup php settings
        if ($ini = ConfigProxy::get('php')) {
            foreach ($ini as $key => $value) {
                $result = ini_set($key, $value);
                LoggerProxy::info('app:init:php:' . $key . ':' . ($result ?: '---'));
            }
        }
    }

    /**
     * Initial Cache Proxy
     *
     * @return void
     * @throws ComponentException
     */
    protected function initCache(): void
    {
        if (ConfigProxy::get('cache', 'enabled') && $adapterName = ConfigProxy::get('cache', 'adapter')) {
            $adapter = ConfigProxy::get('cache', 'pools', $adapterName);
            if (!$adapter) {
                throw new ComponentException("Class `Proxy\\Cache` required configuration for `$adapterName` adapter");
            }
            if (!is_callable($adapter)) {
                throw new ComponentException("Class `Proxy\\Cache` required callable function `$adapterName` adapter");
            }
            $cache = $adapter();
            CacheProxy::setInstance($cache);
        }
    }

    /**
     * Initial Session Proxy
     *
     * @return void
     * @throws ApplicationException
     * @throws ReflectionException
     */
    protected function initData(): void
    {
        $this->data = new Data($this->path);

        if ($data = CacheProxy::get('application.data')) {
            $this->data->setFromArray($data);
        } else {
            $this->data->init();
            CacheProxy::set('application.data', $this->data->toArray(), CacheProxy::TTL_NO_EXPIRY, ['system']);
        }
    }

    /**
     * Initial Session Proxy
     *
     * @return void
     */
    protected function initSession(): void
    {
        $session = new Session();
        $session->setOptions(ConfigProxy::get('session'));
        SessionProxy::setInstance($session);
    }

    /**
     * Initial Messages Proxy
     *
     * @return void
     */
    private function initMessages(): void
    {
        $messages = new Messages();
        $messages->setOptions(ConfigProxy::get('messages'));
        MessagesProxy::setInstance($messages);
    }

    /**
     * Initial Request Proxy
     *
     * @return void
     */
    private function initRequest(): void
    {
        $request = new Request(
            baseUrl: $this->baseUrl,
        );
        RequestProxy::setInstance($request);
    }

    /**
     * Initial Response Proxy
     *
     * @return void
     */
    private function initResponse(): void
    {
        $response = new Response();
        ResponseProxy::setInstance($response);
    }

    /**
     * Initial Router Proxy
     *
     * @return void
     */
    protected function initRouter(): void
    {
        $router = new Router(
            baseUrl: $this->baseUrl,
            path:    RequestProxy::getPath(),
            modules: $this->getData()->getModules(),
            routes:  $this->getData()->getRoutes()
        );
        $router->setOptions(ConfigProxy::get('router'));
        RouterProxy::setInstance($router);
    }

    /**
     * Initial Translator Proxy
     *
     * @return void
     * @throws ConfigurationException
     */
    private function initTranslator(): void
    {
        $translator = new Translator();
        $translator->setOptions(ConfigProxy::get('translator'));
        $translator->init();

        TranslatorProxy::setInstance($translator);
    }

    /**
     * Run application
     *
     * @return void
     * @throws CacheException
     */
    public function run(): void
    {
        $this->process();
        $this->render();
        $this->end();
    }

    /**
     * Process application
     *
     * Note:
     * - Why you don't use "X-" prefix for custom headers?
     * - Because it deprecated ({@link http://tools.ietf.org/html/rfc6648})
     *
     * @return void
     * @throws CacheException
     */
    public function process(): void
    {
        $this->preProcess();
        $this->doProcess();
        $this->postProcess();
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
        // analyse request and find required route
        RouterProxy::process();

        // rewrite request params with route data
        RequestProxy::reset(
            RouterProxy::getDefaultModule(),
            RouterProxy::getDefaultController(),
            RouterProxy::getParams()
        );

        // disable Layout for XmlHttpRequests
        if (RequestProxy::isXmlHttpRequest()) {
            $this->disableLayout();
        }

        // switch to JSON response based on Accept header
        if (RequestProxy::checkAccept(MimeType::JSON)) {
            $this->disableLayout();
            ResponseProxy::setContentType(ResponseType::JSON);
        }
    }

    /**
     * Do process
     *
     * - Dispatch controller
     * - Exceptions handling
     * - Setup layout
     * - Setup response body
     *
     * @return void
     * @throws CacheException
     */
    protected function doProcess(): void
    {
        $module = RequestProxy::getModule();
        $controller = RequestProxy::getController();
        $params = RequestProxy::getParams();
        try {
            // try to dispatch controller
            $result = $this->dispatch($module, $controller, $params);
        } catch (ForbiddenException $e) {
            // dispatch default error controller
            $result = $this->forbidden($e);
        } catch (RedirectException $e) {
            // should return `null` for disable output and setup redirect headers
            $result = $this->redirect($e);
        } catch (Exception $e) {
            // dispatch default error controller
            $result = $this->error($e);
        }

        // setup layout, if needed
        if ($this->hasLayout()) {
            // render view to layout
            // needed for headScript and headStyle helpers
            LayoutProxy::setContent($result->render());
            ResponseProxy::setBody(LayoutProxy::getInstance());
        } else {
            ResponseProxy::setBody($result);
        }
    }

    /**
     * Extension point: post process
     *
     * @return void
     */
    protected function postProcess(): void
    {
        // nothing
    }

    /**
     * Dispatch controller with params
     *
     * Call dispatch from any \Bluz\Package
     *     Application::getInstance()->dispatch($module, $controller, array $params);
     *
     * @param string $module
     * @param string $controller
     * @param array $params
     *
     * @return Controller
     * @throws CacheException
     * @throws CommonException
     * @throws ComponentException
     * @throws ControllerException
     * @throws ReflectionException
     */
    public function dispatch(string $module, string $controller, array $params = []): Controller
    {
        $instance = new Controller($this->getPath(), $module, $controller, $params);

        LoggerProxy::info("app:dispatch:>>>: $module/$controller");
        $this->preDispatch($instance);

        LoggerProxy::info("app:dispatch:===: $module/$controller");
        $this->doDispatch($instance);

        LoggerProxy::info("app:dispatch:<<<: $module/$controller");
        $this->postDispatch($instance);

        return $instance;
    }

    /**
     * Extension point: pre dispatch
     *
     * @param Controller $controller
     *
     * @return void
     *
     * @throws ForbiddenException
     * @throws NotAllowedException
     * @throws NotAcceptableException
     */
    protected function preDispatch(Controller $controller): void
    {
        // check HTTP method
        $this->checkHttpMethod($controller);

        // check ACL privileges
        $this->checkPrivilege($controller);

        // check HTTP Accept header
        $this->checkHttpAccept($controller);
    }

    /**
     * @throws NotAllowedException
     */
    protected function checkHttpMethod($controller): void
    {
        $methods = $this->getData()->getAttribute(
            $controller->getModule(),
            $controller->getController(),
            'method'
        );

        if (empty($methods) || in_array(RequestProxy::getMethod(), $methods, true)) {
            return;
        }

        // prepare list of the allowed methods
        $methods = array_map(function ($method) {
            return $method->value;
        }, $methods);
        throw new NotAllowedException(implode(',', $methods));
    }

    /**
     * @throws ForbiddenException
     */
    protected function checkPrivilege($controller): void
    {
        $privileges = $this->getData()->getAttribute(
            $controller->getModule(),
            $controller->getController(),
            'privilege'
        );

        if (empty($privileges)) {
            return;
        }

        $privileges = array_map('strtolower', $privileges);
        $privileges = array_unique($privileges);

        foreach ($privileges as $privilege) {
            if (AclProxy::isAllowed($controller->getModule(), $privilege)) {
                return;
            }
        }
        throw new ForbiddenException();
    }

    /**
     * @throws NotAcceptableException
     */
    protected function checkHttpAccept($controller): void
    {
        $acceptTypes = $this->getData()->getAttribute(
            $controller->getModule(),
            $controller->getController(),
            'accept'
        );

        // check accept header only if you need this
        // mime type "ANY" allow anything
        if (empty($acceptTypes) || in_array(MimeType::ANY, $acceptTypes)) {
            return;
        }

        $acceptRequest = RequestProxy::getAccept();

        // try to search allowed types
        foreach ($acceptRequest as $type => $q) {
            $mimeType = MimeType::tryFrom($type);
            if ($mimeType && in_array($mimeType, $acceptTypes)) {
                return;
            }
        }
        throw new NotAcceptableException();
    }

    /**
     * Do dispatch
     *
     * @param Controller $controller
     *
     * @return void
     * @throws ReflectionException
     * @throws ComponentException
     * @throws ControllerException
     */
    protected function doDispatch(Controller $controller): void
    {
        $cacheKey = "data." .
            $controller->getModule() . "." .
            $controller->getController() . "." .
            md5(http_build_query($controller->getParams()));

        $cacheTtl = $this->getData()->getAttribute(
            $controller->getModule(),
            $controller->getController(),
            'cache'
        );

        if ($cacheTtl && $data = CacheProxy::get($cacheKey)) {
            $controller->setData($data);
        } else {
            if (
                $types = $this->getData()->getParams(
                    $controller->getModule(),
                    $controller->getController()
                )
            ) {
                $controller->setTypes($types);
            }

            $controller->process();
            if ($cacheTtl) {
                CacheProxy::set(
                    $cacheKey,
                    $controller->getData(),
                    $cacheTtl,
                    ['system', 'data', $controller->getModule() . ':' . $controller->getController()]
                );
            }
        }
    }

    /**
     * Extension point: post dispatch
     *
     * @param Controller $controller
     *
     * @return void
     */
    protected function postDispatch(Controller $controller): void
    {
        // nothing by default
    }

    /**
     * Render send Response
     *
     * @return void
     */
    public function render(): void
    {
        ResponseProxy::send();
    }

    /**
     * Extension point: finally method
     *
     * @return void
     */
    public function end(): void
    {
        // nothing by default
    }
}
