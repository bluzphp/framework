<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\View\Helper;

use Bluz\View\View;

/**
 * Generate HTML and JavaScript for WYSIWYG redactor
 * @link http://imperavi.com/redactor/
 *
 * @param  string $selector
 * @param  array $settings
 * @return string
 */
return
    function ($selector, array $settings = []) {
        /**
        * @var View $this
        */
        $defaultSettings = [
            'imageUpload' => $this->url('media', 'upload'),    // default media upload controller
            'imageManagerJson' => $this->url('media', 'list'), // default images list
            'plugins' => ['imagemanager']
        ];


        $settings = array_replace_recursive($defaultSettings, $settings);
        $settings = json_encode($settings);

        $html = "";
        $html .= $this->headStyle('redactor/redactor.css');
        $html .= $this->headScript(
            'require(["jquery", "redactor", "redactor.imagemanager"], function($) {
                $("' . $selector . '").redactor(' . $settings . ');
            });'
        );
        return $html;
    };
