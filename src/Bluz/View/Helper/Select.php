<?php
/**
 * Copyright (c) 2013 by Bluz PHP Team
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

use Bluz\View\View;

return

/**
 * @author The-Who
 *
 * <code>
 * $this->select("car", [
 * "none" => "No Car"
 * "class-A" => [
 *    'citroen-c1' => 'Citroen C1',
 *    'mercedes-benz-a200' => 'Mercedes Benz A200',
 * ],
 * "class-B" => [
 *    'audi-a1' => 'Audi A1',
 *    'citroen-c3' => 'Citroen C3',
 * ],
 * ], "none", ["id"=>"car"]);
 *
 * <select name="car" id="car">
 *   <option value="none" selected="selected">No car</option>
 *   <optgroup label="class-A">
 *     <option value="citroen-c1">Citroen C1</option>
 *     <option value="mercedes-benz-a200">Mercedes Benz A200</option>
 *   </optgroup>
 *   <optgroup label="class-B">
 *     <option value="audi-a1">Audi A1</option>
 *     <option value="citroen-c3">Citroen C3</option>
 *   </optgroup>
 * </select>
 *
 * </code>
 *
 * @param string $name
 * @param array $options
 * @param array|string $selected
 * @param array $attributes
 * @return \Closure
 */
function ($name, array $options = [], $selected = null, array $attributes = []) {
    /** @var View $this */
    $attributes['name'] = $name;

    if (!is_array($selected)) {
        if ($selected === null) {
            // empty array
            $selected = array();
        } else {
            // convert one option to an array
            $selected = array((string)$selected);
        }
    } elseif (is_array($selected) && count($selected) > 1) {
        $attributes['multiple'] = 'multiple';
    }

    /**
     * @param $value
     * @param $text
     * @return string
     */
    $buildOption = function($value, $text) use ($selected) {
        $value = (string) $value;
        $option = array('value' => $value);
        if (in_array($value, $selected)) {
            $option['selected'] = 'selected';
        }
        return '<option '. $this->attributes($option) . '>' . htmlspecialchars((string) $text, ENT_QUOTES,  "UTF-8", false) . '</option>';
    };


    $result = [];
    foreach ($options as $value => $text) {
        if (is_array($text)) {
            // optgroup support
            // create a list of sub-options
            $subOptions = array();
            foreach ($text as $subValue => $subText) {
                $subOptions[] = $buildOption($subValue, $subText);
            }
            // build string from array
            $subOptions = "\n".join("\n", $subOptions)."\n";

            $result[] = '<optgroup '.$this->attributes(['label' => $value]).'>'.$subOptions.'</optgroup>';

        } else {
            $result[] = $buildOption($value, $text);
        }
    }
    $result = "\n".join("\n", $result)."\n";
    return '<select '. $this->attributes($attributes)  .'>' . $result . '</select>';

};