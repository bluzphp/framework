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
namespace Bluz\Layout;

use Bluz\Common\Container\RegularAccess;
use Bluz\View\View;

/**
 * Layout
 *
 * @package  Bluz\Layout
 *
 * @method   array|null breadCrumbs(array $data = [])
 * @method   string|null headScript(string $script = null)
 * @method   string|null headStyle(string $style = null, $media = 'all')
 * @method   string|null link(string $src = null, string $rel = 'stylesheet')
 * @method   string|null meta(string $name = null, string $content = null)
 * @method   string|null title(string $title = null, $position = 'replace', $separator = ' :: ')
 *
 * @author   Anton Shevchuk
 * @created  08.07.11 11:49
 */
class Layout extends View
{
    use RegularAccess;

    /**
     * Content container, usually is instance of View
     * @var mixed
     */
    protected $content;

    /**
     * __construct
     *
     * @return self
     */
    public function __construct()
    {
        // init layout helper path
        $this->addHelperPath(dirname(__FILE__) . '/Helper/');

        // init view helper path
        parent::__construct();
    }

    /**
     * Set content
     *
     * @return View
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set content
     *
     * @param View|callable $content
     * @return void
     */
    public function setContent($content)
    {
        try {
            if (is_callable($content)) {
                $content = $content();
            }
            $this->content = $content;
        } catch (\Exception $e) {
            $this->content = $e->getMessage();
        }
    }
}
