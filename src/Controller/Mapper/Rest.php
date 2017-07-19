<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Controller\Mapper;

use Bluz\Http\RequestMethod;

/**
 * Rest
 *
 * @package  Bluz\Rest
 * @author   Anton Shevchuk
 */
class Rest extends AbstractMapper
{
    /**
     * Process request
     *
     * @return void
     */
    protected function prepareRequest()
    {
        parent::prepareRequest();

        $params = $this->params;

        if (count($params)) {
            $primaryKeys = $this->crud->getPrimaryKey();
            $primaryValues = explode('-', array_shift($params));

            $this->primary = array_combine($primaryKeys, $primaryValues);
        }
        if (count($params)) {
            $this->relation = array_shift($params);
        }
        if (count($params)) {
            $this->relationId = array_shift($params);
        }

        // OPTIONS
        if (RequestMethod::OPTIONS === $this->method) {
            $this->data = array_keys($this->map);
        }
    }

    /**
     * Prepare params
     *
     * @return array
     */
    protected function prepareParams(): array
    {
        return [
            'crud' => $this->crud,
            'primary' => $this->primary,
            'data' => $this->data,
            'relation' => $this->relation,
            'relationId' => $this->relationId
        ];
    }
}
