<?php
/**
 * @package     Bluecoder.JFilters
 *
 * @copyright   Copyright © 2023 Blue-Coder.com. All rights reserved.
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

        'customizer.init' => [
            SourceListener::class => ['initCustomizer', 10],
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