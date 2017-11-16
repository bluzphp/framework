<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Layout;

use Bluz\Common\Container\RegularAccess;
use Bluz\View\View;

/**
 * Layout
 *
 * @package Bluz\Layout
 * @author  Anton Shevchuk
 * @link    https://github.com/bluzphp/framework/wiki/Layout
 *
 * @method array|null  breadCrumbs(array $data = [])
 * @method string|null headScript(string $src = null, array $attributes = [])
 * @method string|null headScriptBlock(string $code = null)
 * @method string|null headStyle(string $href = null, string $media = 'all')
 * @method string|null link(string $src = null, string $rel = 'stylesheet')
 * @method string|null meta(string $name = null, string $content = null)
 * @method string|null title(string $title = null)
 * @method string titleAppend(string $title, string $separator = ' :: ')
 * @method string titlePrepend(string $title, string $separator = ' :: ')
 */
class Layout extends View
{
    use RegularAccess;

    /**
     * @var mixed content container, usually is instance of View
     */
    protected $content;

    /**
     * Layout constructor
     *  - init Layout helpers
     *  - call parent View constructor
     */
    public function __construct()
    {
        // init layout helper path
        $this->addHelperPath(__DIR__ . '/Helper/');

        // init view helper path
        parent::__construct();
    }

    /**
     * Get content
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
     * @param  View|callable $content
     *
     * @return void
     */
    public function setContent($content) : void
    {
        try {
            $this->content = value($content);
        } catch (\Exception $e) {
            $this->content = $e->getMessage();
        }
    }
}
