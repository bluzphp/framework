<?php
/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

/**
 * Example controller for test mapper
 *
 * @author   Anton Shevchuk
 */

namespace Application;

/**
 * @return array
 */
return function ($crud, $primary, $data, $relation = null, $relationId = null) {

    return [
        'crud' => $crud,
        'primary' => $primary,
        'data' => $data,
        'relation' => $relation,
        'relationId' => $relationId,
    ];
};
