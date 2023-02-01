<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Controller\Mapper;

/**
 * Link
 *
 * @package  Bluz\Controller\Mapper
 * @author   Anton Shevchuk
 */
class Link
{
    /**
     * @var string
     */
    protected string $module;

    /**
     * @var string
     */
    protected string $controller;

    /**
     * @var string|null
     */
    protected ?string $acl = null;

    /**
     * @var array
     */
    protected array $fields = [];

    /**
     * Constructor of Link
     *
     * @param string $module
     * @param string $controller
     */
    public function __construct(string $module, string $controller)
    {
        $this->setModule($module);
        $this->setController($controller);
    }

    /**
     * Set ACL privilege
     *
     * @param string $acl
     *
     * @return Link
     */
    public function acl(string $acl): Link
    {
        $this->setAcl($acl);
        return $this;
    }

    /**
     * Set filters for data
     *
     * @param array $fields
     *
     * @return Link
     */
    public function fields(array $fields): Link
    {
        $this->setFields($fields);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getModule(): ?string
    {
        return $this->module;
    }

    /**
     * @param string $module
     */
    protected function setModule(string $module): void
    {
        $this->module = $module;
    }

    /**
     * @return string|null
     */
    public function getController(): ?string
    {
        return $this->controller;
    }

    /**
     * @param string $controller
     */
    protected function setController(string $controller): void
    {
        $this->controller = $controller;
    }

    /**
     * @return string|null
     */
    public function getAcl(): ?string
    {
        return $this->acl;
    }

    /**
     * @param string $acl
     */
    protected function setAcl(string $acl): void
    {
        $this->acl = $acl;
    }

    /**
     * @return array
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * Setup data filters
     *
     * @param array $fields
     */
    protected function setFields(array $fields): void
    {
        $this->fields = $fields;
    }
}
