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
namespace Bluz\Proxy;

use Bluz\Layout\Layout as Instance;
use Bluz\View\View;

/**
 * Proxy to Layout
 *
 * Example of usage
 *     use Bluz\Proxy\Layout;
 *
 *     Layout::title('Homepage');
 *     Layout::set('description', 'some page description');
 *
 * @package  Bluz\Proxy
 *
 * @method   static Instance getInstance()
 *
 * @method   static View getContent()
 * @see      Bluz\Layout\Layout::getContent()
 * @method   static void setContent($content)
 * @see      Bluz\Layout\Layout::setContent()
 * @method   static void setPath($path)
 * @see      Bluz\View\View::setPath()
 * @method   static void setTemplate($file)
 * @see      Bluz\View\View::setTemplate()
 *
 * @method   static void set($key, $value)
 * @see      Bluz\Common\Container\RegularAccess::set()
 * @method   static mixed get($key)
 * @see      Bluz\Common\Container\RegularAccess::get()
 * @method   static bool contains($key)
 * @see      Bluz\Common\Container\RegularAccess::contains()
 * @method   static void delete($key)
 * @see      Bluz\Common\Container\RegularAccess::delete()
 *
 * @method   static array|null breadCrumbs(array $data = [])
 * @method   static string|null headScript(string $script = null)
 * @method   static string|null headStyle(string $style = null, $media = 'all')
 * @method   static string|null link(string $src = null, string $rel = 'stylesheet')
 * @method   static string|null meta(string $name = null, string $content = null)
 * @method   static string|null title(string $title = null, $position = 'replace', $separator = ' :: ')
 *
 * @author   Anton Shevchuk
 * @created  02.10.2014 10:45
 */
class Layout extends AbstractProxy
{
    /**
     * Constants for define positions
     */
    const POS_PREPEND = Instance::POS_PREPEND;
    const POS_REPLACE = Instance::POS_REPLACE;
    const POS_APPEND = Instance::POS_APPEND;

    /**
     * Init instance
     *
     * @return Instance
     */
    protected static function initInstance()
    {
        $instance = new Instance();
        $instance->setOptions(Config::getData('layout'));
        return $instance;
    }
}
