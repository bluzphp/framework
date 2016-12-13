<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\View;

use Bluz\Application\Application;
use Bluz\Auth\AbstractRowEntity;
use Bluz\Common\Container;
use Bluz\Common\Helper;
use Bluz\Common\Options;
use Bluz\Response\ResponseTrait;

/**
 * View - simple template engine with native PHP syntax
 *
 * @package  Bluz\View
 * @author   Anton Shevchuk
 * @author   ErgallM
 * @link     https://github.com/bluzphp/framework/wiki/View
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
 * @method string|null headScript(string $script = null)
 * @method string|null headStyle(string $style = null, $media = 'all')
 * @method string|bool module(string $module = null)
 * @method string partial($__template, $__params = [])
 * @method string partialLoop($template, $data = [], $params = [])
 * @method string radio($name, $value = null, $checked = false, array $attributes = [])
 * @method string redactor($selector, array $settings = [])
 * @method string script(string $script)
 * @method string select($name, array $options = [], $selected = null, array $attributes = [])
 * @method string style(string $style, $media = 'all')
 * @method string|null url(string $module, string $controller, array $params = [], bool $checkAccess = false)
 * @method AbstractRowEntity|null user()
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
     * Constants for define positions
     */
    const POS_PREPEND = 'prepend';
    const POS_REPLACE = 'replace';
    const POS_APPEND = 'append';

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
     */
    public function __construct()
    {
        // initial default helper path
        $this->addHelperPath(dirname(__FILE__) . '/Helper/');
    }

    /**
     * List of packed properties
     *
     * @return string[]
     */
    public function __sleep()
    {
        return ['baseUrl', 'container', 'helpersPath', 'path', 'partialPath', 'template'];
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
            if (!file_exists($this->path . '/' . $this->template)
                || !is_file($this->path . '/' . $this->template)) {
                throw new ViewException("Template '{$this->template}' not found");
            }
            extract($this->container);
            require $this->path . '/' . $this->template;
        } catch (\Exception $e) {
            // clean output
            ob_end_clean();
            // @codeCoverageIgnoreStart
            if (Application::getInstance()->isDebug()) {
                return $e->getMessage() ."\n<br/>". $e->getTraceAsString();
            }
            // @codeCoverageIgnoreEnd
            // nothing for production
            return '';
        }
        return ob_get_clean();
    }

    /**
     * {@inheritdoc}
     *
     * @param  string $path
     * @return void
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * {@inheritdoc}
     *
     * @param  string $file
     * @return void
     */
    public function setTemplate($file)
    {
        $this->template = $file;
    }

    /**
     * Add partial path for use inside partial and partialLoop helpers
     *
     * @param  string $path
     * @return View
     */
    public function addPartialPath($path)
    {
        $this->partialPath[] = $path;
        return $this;
    }
}
