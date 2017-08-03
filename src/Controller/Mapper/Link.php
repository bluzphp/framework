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
    protected $module;

    /**
     * @var string
     */
    protected $controller;

    /**
     * @var array
     */
    protected $params;

    /**
     * @var string
     */
    protected $acl;

    /**
     * @var array
     */
    protected $filters = [];

    /**
     * Constructor of Link
     *
     * @access  public
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
    public function acl(string $acl) : Link
    {
        $this->setAcl($acl);
        return $this;
    }

    /**
     * Set filters for data
     *
     * @param array $filters
     *
     * @return Link
     */
    public function filter(array $filters) : Link
    {
        $this->setFilters($filters);
        return $this;
    }

    /**
     * @return string
     */
    public function getModule(): string
    {
        return $this->module;
    }

    /**
     * @param string $module
     */
    protected function setModule(string $module)
    {
        $this->module = $module;
    }

    /**
     * @return string
     */
    public function getController(): string
    {
        return $this->controller;
    }

    /**
     * @param string $controller
     */
    protected function setController(string $controller)
    {
        $this->controller = $controller;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @param array $params
     */
    public function setParams(array $params)
    {
        $this->params = $params;
    }

    /**
     * @return string|null
     */
    public function getAcl()
    {
        return $this->acl;
    }

    /**
     * @param string $acl
     */
    protected function setAcl(string $acl)
    {
        $this->acl = $acl;
    }

    /**
     * @return array
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * Setup data filters
     *
     * @param array $filters
     */
    protected function setFilters(array $filters)
    {
        $this->filters = $filters;
    }
}
