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
 * Crud
 *
 * @package  Bluz\Rest
 * @author   Anton Shevchuk
 */
class Crud extends AbstractMapper
{
    protected function prepareParams() : array
    {
        return [
            'crud' => $this->crud,
            'primary' => $this->primary,
            'data' => $this->data
        ];
    }
}
