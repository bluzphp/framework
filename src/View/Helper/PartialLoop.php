<?php
/**
 * Copyright (c) 2012 by Bluz PHP Team
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

/**
 * @namespace
 */
namespace Bluz\View\Helper;

use Bluz\Application;
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
    if (!file_exists($this->path .'/'. $template)) {
        throw new ViewException("Template '{$template}' not found");
    }

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

    foreach ($data as $key => $value) {
        $params['key'] = $key;
        $params['value'] = $value;
        $this->partial($template, $params);
    }
};
