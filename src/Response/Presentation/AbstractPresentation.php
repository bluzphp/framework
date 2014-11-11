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
 *
 * @author   Anton Shevchuk
 * @created  10.11.2014 17:46
 */
abstract class AbstractPresentation
{
    /**
     * @var AbstractResponse Instance
     */
    protected $response;

    /**
     * Create instance
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
