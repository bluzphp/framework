<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Proxy;

use Bluz\Common\Container\RegularAccess;
use Bluz\Layout\Layout as Instance;
use Bluz\View\View;

/**
 * Proxy to Layout
 *
 * Example of usage
 * <code>
 *     use Bluz\Proxy\Layout;
 *
 *     Layout::title('Homepage');
 *     Layout::set('description', 'some page description');
 * </code>
 *
 * @package  Bluz\Proxy
 * @author   Anton Shevchuk
 *
 * @method   static Instance getInstance()
 *
 * @method   static View getContent()
 * @see      Instance::getContent()
 * @method   static void setContent($content)
 * @see      Instance::setContent()
 * @method   static void setPath($path)
 * @see      View::setPath()
 * @method   static void setTemplate($file)
 * @see      View::setTemplate()
 *
 * @method   static void set($key, $value)
 * @see      RegularAccess::set()
 * @method   static mixed get($key)
 * @see      RegularAccess::get()
 * @method   static bool contains($key)
 * @see      RegularAccess::contains()
 * @method   static void delete($key)
 * @see      RegularAccess::delete()
 *
 * @method static string ahref(string $text, mixed $href, array $attributes = [])
 * @method static array|null breadCrumbs(array $data = [])
 * @method static string|null headScript(string $src = null, array $attributes = [])
 * @method static string|null headScriptBlock(string $code = null)
 * @method static string|null headStyle(string $href = null, string $media = 'all')
 * @method static string|null link(string $src = null, string $rel = 'stylesheet')
 * @method static string|null meta(string $name = null, string $content = null)
 * @method static string|null title(string $title = null, $position = 'replace', $separator = ' :: ')
 */
class Layout
{
    use ProxyTrait;

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
