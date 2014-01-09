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
     * Setup redactorjs
     *
     * @param string $selector
     * @param array $settings
     * @return string
     */
    function ($selector, array $settings = []) {
    /** @var View $this */
    $defaultSettings = [
        'imageUpload' => $this->url('media', 'upload') // default media upload controller
    ];

    $settings = array_replace_recursive($defaultSettings, $settings);
    $settings = json_encode($settings);

    $html = "";
    $html .= $this->headScript('redactor/redactor.js');
    $html .= $this->headStyle('redactor/redactor.css');
    $html .= $this->headScript(
        'require(["jquery", "redactor"], function($) {
            $("' . $selector . '").redactor(' . $settings . ');
        });'
    );
    return $html;
    };
