<?php
/**
 * @package     Bluecoder.JFilters
 *
 * @copyright   Copyright © 2024 - 2025 Blue-Coder.com. All rights reserved.
 * @license     GNU General Public License 2 or later, see COPYING.txt for license details.
 * @see: templates/yootheme/packages/builder-joomla-source/bootstrap.php
 */

use Bluecoder\Plugin\System\JfiltersYootheme\LoadBuilderConfig;
use Bluecoder\Plugin\System\JfiltersYootheme\LoadTemplateUrl;
use Bluecoder\Plugin\System\JfiltersYootheme\MatchTemplate;
use Bluecoder\Plugin\System\JfiltersYootheme\SourceListener;
use YOOtheme\Builder;
use YOOtheme\Builder\BuilderConfig;
use YOOtheme\Builder\Joomla\Fields\Type\FieldsType;

/**
 * @see: templates/yootheme/packages/builder-joomla-source/bootstrap.php
 */
return [
    'extend' => [
        Builder::class => static function (Builder $builder): void {
            $builder->addTypePath(__DIR__ . '/elements/*/element.json');
        }
    ],
    'events' => [
        // Declare the Graphp QL query and types
        'source.init' => [SourceListener::class => 'initSource'],
        // Match the template inside the customizer
        'builder.template' => [MatchTemplate::class => '@handle'],
        'builder.template.load' => [LoadTemplateUrl::class => '@handle'],
        // YT 4,5 event for initializing the customizer
        BuilderConfig::class => [LoadBuilderConfig::class => ['handle', 10]],
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