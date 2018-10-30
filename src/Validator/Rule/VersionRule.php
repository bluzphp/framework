<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Validator\Rule;

/**
 * Check for version format
 *
 * @package Bluz\Validator\Rule
 * @link    http://semver.org/
 */
class VersionRule extends AbstractRule
{
    /**
     * @var string error template
     */
    protected $description = 'must be a valid version number';

    /**
     * Check for version format
     *
     * @param  string $input
     *
     * @return bool
     */
    public function validate($input): bool
    {
        $pattern = '/^\d+\.\d+\.\d+([+-][^+-][0-9A-Za-z-.]*)?$/';

        return (bool)preg_match($pattern, $input);
    }
}
