<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\View\Helper;

use Bluz\View\View;
use Bluz\View\ViewException;

/**
 * Render partial script in loop
 *
 * Example of usage:
 * <code>
 *     <?php
 *         $data = [2, 4, 6, 8];
 *         $this->partialLoop('tr.phtml', $data, ['colspan'=>2]);
 *     ?>
 *     <?php
 *      <tr>
 *        <th>
 *          <?=$key?>
 *        </th>
 *        <td colspan="<?=$colspan?>">
 *          <?=$value?>
 *        </td>
 *      </tr>
 *     ?>
 * </code>
 *
 * @param  string $template
 * @param  array  $data
 * @param  array  $params
 * @return string
 * @throws ViewException|\InvalidArgumentException
 */
return
    function ($template, $data = [], $params = []) {
        /**
         * @var View $this
         */
        if (!is_array($data)
            && !($data instanceof \Traversable)
            && !(is_object($data) && method_exists($data, 'toArray'))
        ) {
            throw new \InvalidArgumentException('PartialLoop helper requires iterable data');
        }

        if (is_object($data)
            && (!$data instanceof \Traversable)
            && method_exists($data, 'toArray')
        ) {
            $data = $data->toArray();
        }

        $result = [];
        foreach ($data as $key => $value) {
            $params['partialKey'] = $key;
            $params['partialValue'] = $value;
            $result[] = $this->partial($template, $params);
        }
        return join('', $result);
    };
