<?php
/**
 * @package     Bluecoder.JFilters
 *
 * @copyright   Copyright © 2024 Blue-Coder.com. All rights reserved.
 * @license     GNU General Public License 2 or later, see COPYING.txt for license details.
 */

use Bluecoder\Plugin\System\JfiltersYootheme\TemplateListener;
use Bluecoder\Plugin\System\JfiltersYootheme\SourceListener;
use YOOtheme\Builder\Joomla\Fields\Type\FieldsType;

/*
 * As a guide look at the file: templates/yootheme/vendor/yootheme/theme-joomla-finder/bootstrap.php
 */

return [
    'events' => [
        'source.init' => [
            SourceListener::class => 'initSource',
        ],
        // YT 3 event for initializing the customizer
        'customizer.init' => [
            SourceListener::class => ['initCustomizerYT3', 10],
        ],
        // YT 4 event for initializing the customizer
        'YOOtheme\Builder\BuilderConfig' => [
            SourceListener::class => ['initCustomizerYT4', 10],
        ],
        'builder.template' => [
            TemplateListener::class => 'matchTemplate',
        ],
    ],

    /*
     * We need to register that service for the rendering of the custom fields.
     * Otherwise a ServiceNotFoundException is thrown.
     */
    'services' => [
        'Article.fields' => function () {
            return new FieldsType('com_content.article');
        }
    ],
];