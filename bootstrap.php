<?php
/**
 * @package     Bluecoder.JFilters
 *
 * @copyright   Copyright © 2022 Blue-Coder.com. All rights reserved.
 * @license     GNU General Public License 2 or later, see COPYING.txt for license details.
 */

use Bluecoder\Plugin\System\JfiltersYootheme\TemplateListener;
use Bluecoder\Plugin\System\JfiltersYootheme\SourceListener;

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
    ]
];