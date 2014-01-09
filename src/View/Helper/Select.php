<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
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
    $buildOption = function ($value, $text) use ($selected) {
        $value = (string)$value;
        $option = array('value' => $value);
        if (in_array($value, $selected)) {
            $option['selected'] = 'selected';
        }
        return '<option ' . $this->attributes($option) . '>' . htmlspecialchars(
            (string)$text,
            ENT_QUOTES,
            "UTF-8",
            false
        ) . '</option>';
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
            $subOptions = "\n" . join("\n", $subOptions) . "\n";

            $result[] = '<optgroup ' . $this->attributes(['label' => $value]) . '>' . $subOptions . '</optgroup>';

        } else {
            $result[] = $buildOption($value, $text);
        }
    }
    $result = "\n" . join("\n", $result) . "\n";
    return '<select ' . $this->attributes($attributes) . '>' . $result . '</select>';

    };
