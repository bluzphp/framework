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
namespace Bluz\Response;

/**
 * Response Trait
 *
 * @package  Bluz\Response
 * @author   Anton Shevchuk
 */
trait ResponseTrait
{
    /**
     * @access public
     * @param  string $type
     * @return mixed
     */
    public function render($type = 'HTML')
    {
        // switch statement by response type
        switch (strtoupper($type)) {
            case 'CLI':
            case 'JSON':
                return $this->jsonSerialize();
            case 'HTML':
                return $this->__toString();
            default:
                return '';
        }
    }
}
