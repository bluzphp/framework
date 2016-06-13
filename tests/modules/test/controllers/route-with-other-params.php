<?php
/**
 * Example route with params
 *
 * @category Application
 *
 * @author   dark
 * @created  18.12.13 18:39
 */
namespace Application;

/**
 * @route /test/route-with-other-params/{$alias}(.*)
 * @param $alias
 * @return bool
 */
return function ($alias) {
    return false;
};
