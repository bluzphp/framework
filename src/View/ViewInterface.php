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

/**
 * ViewInterface
 *
 * @package  Bluz\View
 *
 * @author   Anton Shevchuk
 * @created  19.02.13 15:25
 */
interface ViewInterface
{
    /**
     * Setup path to templates
     *
     * Example of usage
     *     $view->setPath('/modules/users/views');
     *
     * @param string $path
     * @return ViewInterface
     */
    public function setPath($path);

    /**
     * Setup template
     *
     * Example of usage
     *     $view->setTemplate('index.phtml');
     *
     * @param string $file
     * @return ViewInterface
     */
    public function setTemplate($file);

    /**
     * Merge data from array
     *
     * @param array $data
     * @return ViewInterface
     */
    public function setData($data = array());

    /**
     * Get data as array
     *
     * @return array
     */
    public function getData();
}
