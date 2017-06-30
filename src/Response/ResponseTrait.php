<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

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
     * @return string
     */
    abstract public function jsonSerialize();

    /**
     * @return string
     */
    abstract public function __toString();

    /**
     * Render object as HTML or JSON
     *
     * @param  string $type
     *
     * @return string
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
