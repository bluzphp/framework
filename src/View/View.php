<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\View;

use Bluz\Auth\AbstractIdentity;
use Bluz\Common\Container;
use Bluz\Common\Exception\CommonException;
use Bluz\Common\Helper;
use Bluz\Common\Options;
use Bluz\Proxy\Logger;
use Bluz\Response\ResponseTrait;

/**
 * View - simple template engine with native PHP syntax
 *
 * @package  Bluz\View
 * @author   Anton Shevchuk
 * @author   ErgallM
 * @link     https://github.com/bluzphp/framework/wiki/View
 *
 * @see \Bluz\View\Helper\
 *
 * @method string ahref(string $text, mixed $href, array $attributes = [])
 * @method string api(string $module, string $method, $params = [])
 * @method string attributes(array $attributes = [])
 * @method string baseUrl(string $file = null)
 * @method string checkbox($name, $value = null, $checked = false, array $attributes = [])
 * @method string|bool controller(string $controller = null)
 * @method string|View dispatch($module, $controller, $params = [])
 * @method string exception(\Exception $exception)
 * @method string gravatar($email, $size = 80, $default = 'mm', $rate = 'g')
 * @method bool hasModule(string $module)
 * @method string|null headScript(string $src = null, array $attributes = [])
 * @method string|null headScriptBlock(string $code = null)
 * @method string|null headStyle(string $href = null, string $media = 'all')
 * @method string|bool module(string $module = null)
 * @method string partial($__template, $__params = [])
 * @method string partialLoop($template, $data = [], $params = [])
 * @method string radio($name, $value = null, $checked = false, array $attributes = [])
 * @method string redactor($selector, array $settings = [])
 * @method string script(string $src, array $attributes = [])
 * @method string scriptBlock(string $code)
 * @method string select($name, array $options = [], $selected = null, array $attributes = [])
 * @method string style(string $href, $media = 'all')
 * @method string styleBlock(string $code, $media = 'all')
 * @method string|null url(string $module, string $controller, array $params = [], bool $checkAccess = false)
 * @method AbstractIdentity|null user()
 * @method void widget($module, $widget, $params = [])
 */
class View implements ViewInterface, \JsonSerializable
{
    use Container\Container;
    use Container\JsonSerialize;
    use Container\MagicAccess;
    use Options;
    use Helper;
    use ResponseTrait;

    /**
     * @var string base url
     */
    protected $baseUrl;

    /**
     * @var string path to template
     */
    protected $path;

    /**
     * @var array paths to partial
     */
    protected $partialPath = [];

    /**
     * @var string template name
     */
    protected $template;

    /**
     * Create view instance, initial default helper path
     *
     * @throws CommonException
     */
    public function __construct()
    {
        // initial default helper path
        $this->addHelperPath(__DIR__ . '/Helper/');
    }

    /**
     * Render like string
     *
     * @return string
     */
    public function __toString()
    {
        ob_start();
        try {
            if (
                !file_exists($this->path . DIRECTORY_SEPARATOR . $this->template)
                || !is_file($this->path . DIRECTORY_SEPARATOR . $this->template)
            ) {
                throw new ViewException("Template `{$this->template}` not found");
            }
            extract($this->container, EXTR_SKIP);
            include $this->path . DIRECTORY_SEPARATOR . $this->template;
        } catch (\Exception $e) {
            // save error to log
            Logger::exception($e);
            // clean output
            ob_clean();
        }
        return (string) ob_get_clean();
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * {@inheritdoc}
     *
     * @param string $path
     *
     * @return void
     */
    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getTemplate(): ?string
    {
        return $this->template;
    }

    /**
     * {@inheritdoc}
     *
     * @param string $file
     *
     * @return void
     */
    public function setTemplate(string $file): void
    {
        $this->template = $file;
    }

    /**
     * Add partial path for use inside partial and partialLoop helpers
     *
     * @param string $path
     *
     * @return void
     */
    public function addPartialPath(string $path): void
    {
        $this->partialPath[] = $path;
    }
}
