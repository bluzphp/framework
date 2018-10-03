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
     * @return string
     */
    public function getPath(): ?string;

    /**
     * Setup path to templates
     *
     * Example of usage
     *     $view->setPath('/modules/users/views');
     *
     * @param  string $path
     *
     * @return void
     */
    public function setPath($path): void;

    /**
     * Get template
     *
     * Example of usage
     *     $view->getTemplate();
     *
     * @return string
     */
    public function getTemplate(): ?string;

    /**
     * Setup template
     *
     * Example of usage
     *     $view->setTemplate('index.phtml');
     *
     * @param  string $file
     *
     * @return void
     */
    public function setTemplate($file): void;

    /**
     * Merge data from array
     *
     * @param  array $data
     *
     * @return void
     */
    public function setFromArray(array $data): void;

    /**
     * Get data as array
     *
     * @return array
     */
    public function toArray(): array;
}
