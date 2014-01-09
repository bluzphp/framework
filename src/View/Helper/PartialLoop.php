<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\View\Helper;

use Bluz\Application\Application;
use Bluz\View\View;
use Bluz\View\ViewException;

return
    /**
     * partial loop
     *
     * <pre>
     * <code>
     * <?php
     *  $data = array(2,4,6,8);
     *  $this->partialLoop('tr.phtml', $data, array('colspan'=>2));
     * ?>
     * <?php
     *  <tr>
     *    <th>
     *      <?=$key?>
     *    </th>
     *    <td colspan="<?=$colspan?>">
     *      <?=$value?>
     *    </td>
     *  </tr>
     * ?>
     * </code>
     * </pre>
     *
     * @param       $template
     * @param array $data
     * @param array $params
     * @throws ViewException|\InvalidArgumentException
     * @return string
     */
    function ($template, $data = array(), $params = array()) {
    /** @var View $this */
    if (!is_array($data)
        && (!$data instanceof \Traversable)
        && (is_object($data) && !method_exists($data, 'toArray'))
    ) {
        throw new \InvalidArgumentException('PartialLoop helper requires iterable data');
    }

    if (is_object($data)
        && (!$data instanceof \Traversable)
        && method_exists($data, 'toArray')
    ) {
        $data = $data->toArray();
    }

    $result = array();
    foreach ($data as $key => $value) {
        $params['partialKey'] = $key;
        $params['partialValue'] = $value;
        $result[] = $this->partial($template, $params);
    }
    return join('', $result);
    };
