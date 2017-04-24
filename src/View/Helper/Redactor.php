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

        $html = '';
        $html .= $this->style('redactor/redactor.css');
        $html .= $this->scriptBlock(
            'require(["jquery", "redactor", "redactor.imagemanager"], function($) {
                $("' . $selector . '").redactor(' . $settings . ');
            });'
        );
        return $html;
    };
