<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\View;

/**
 * View Interface
 *
 * @package  Bluz\View
 * @author   Anton Shevchuk
 */
interface ViewInterface
{
    /**
     * Get path to templates
     *
     * Example of usage
     *     $view->getPath();
     *
     * @return ViewInterface
     */
    public function getPath();

    /**
     * Setup path to templates
     *
     * Example of usage
     *     $view->setPath('/modules/users/views');
     *
     * @param  string $path
     *
     * @return ViewInterface
     */
    public function setPath($path);

    /**
     * Get template
     *
     * Example of usage
     *     $view->getTemplate();
     *
     * @return ViewInterface
     */
    public function getTemplate();

    /**
     * Setup template
     *
     * Example of usage
     *     $view->setTemplate('index.phtml');
     *
     * @param  string $file
     *
     * @return ViewInterface
     */
    public function setTemplate($file);

    /**
     * Merge data from array
     *
     * @param  array $data
     *
     * @return ViewInterface
     */
    public function setFromArray(array $data);

    /**
     * Get data as array
     *
     * @return array
     */
    public function toArray();
}
