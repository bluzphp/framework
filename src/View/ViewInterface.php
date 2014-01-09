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
 * ViewInterface
 *
 * @category Bluz
 * @package  View
 *
 * @author   Anton Shevchuk
 * @created  19.02.13 15:25
 */
interface ViewInterface
{
    /**
     * setup path to templates
     *
     * <code>
     * $view->setPath('/modules/users/views');
     * </code>
     *
     * @param string $path
     * @return ViewInterface
     */
    public function setPath($path);

    /**
     * setup template
     *
     * <code>
     * $view->setTemplate('index.phtml');
     * </code>
     *
     * @param string $file
     * @return ViewInterface
     */
    public function setTemplate($file);

    /**
     * merge data from array
     *
     * @param array $data
     * @return ViewInterface
     */
    public function setData($data = array());

    /**
     * get data as array
     *
     * @return array
     */
    public function getData();
}
