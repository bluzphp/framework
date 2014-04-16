<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\View;

/**
 * View
 *
 * @category Bluz
 * @package  View
 *
 * @author   Anton Shevchuk
 * @created  08.07.11 11:49
 *
 * @property mixed content
 */
class Layout extends View
{
    /**
     * @var mixed
     */
    protected $content;

    /**
     * Set content
     *
     * @param mixed $content
     * @return View
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
        return $this;
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
}
