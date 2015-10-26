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
namespace Bluz\Response\Presentation;

use Bluz\Response\AbstractResponse;

/**
 * AbstractPresentation
 *
 * @package  Bluz\Response\Presentation
 * @author   Anton Shevchuk
 */
abstract class AbstractPresentation
{
    /**
     * @var AbstractResponse instance of Response
     */
    protected $response;

    /**
     * Create instance
     *
     * @param AbstractResponse $response
     */
    public function __construct($response)
    {
        $this->response = $response;
    }

    /**
     * Process for change response
     *
     * @return void
     */
    abstract public function process();
}
