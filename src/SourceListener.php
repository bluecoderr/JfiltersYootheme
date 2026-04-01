<?php
/**
 * @package     Bluecoder.JFilters
 *
 * @copyright   Copyright © 2024 - 2026 Blue-Coder.com. All rights reserved.
 * @license     GNU General Public License 2 or later, see COPYING.txt for license details.
 */

namespace Bluecoder\Plugin\System\JfiltersYootheme;

\defined('_JEXEC') or die();

use Joomla\Component\Fields\Administrator\Helper\FieldsHelper;
use YOOtheme\Builder\Joomla\Fields\Type;

class SourceListener
{
    public static function initSource($source)
    {
        $query = [
            JfiltersQueryType::config(),
            JfiltersItemsQueryType::config()
        ];

        $types = [
            'JFiltersResults' => ['JFiltersResults', JfiltersType::config()],
            'JFiltersResultsItem' => ['JFiltersResultsItem', JfiltersItemType::config()],
        ];

        $source->objectType('SqlField', Type\SqlFieldType::config());
        $source->objectType('ValueField', Type\ValueFieldType::config());
        $source->objectType('MediaField', Type\MediaFieldType::config());
        $source->objectType('ChoiceField', Type\ChoiceFieldType::config());
        $source->objectType('ChoiceFieldString', Type\ChoiceFieldStringType::config());

        foreach ($query as $args) {
            $source->queryType($args);
        }

        foreach ($types as $key => $args) {
            // Call this before the custom fields' creation. It affects the order of the source list options.
            $source->objectType(...$args);

            if ($key == 'JFiltersResultsItem') {
                // Add custom fields for content
                $context = 'com_content.article';
                if ($fields = FieldsHelper::getFields($context)) {
                    static::configFields($source, $key, $context, $fields);
                }

                // Add custom fields for contacts
                $context = 'com_contact.contact';
                if ($fields = FieldsHelper::getFields($context)) {
                    static::configFields($source, $key, $context, $fields);
                }
            }
        }
    }

    protected static function configFields($source, $type, $context, array $fields)
    {
        // add field on type
        $source->objectType(
            $type,
            $config = [
                'fields' => [
                    'field' => [
                        'type' => ($fieldType = "{$type}Fields"),
                        'extensions' => [
                            'call' => Type\FieldsType::class . '::field',
                        ],
                    ],
                ],
            ]
        );

        if ($type === 'JFiltersResultsItem') {
            $source->objectType('TagItem', $config);
        }

        // configure field type
        $source->objectType($fieldType, Type\FieldsType::config($source, $type, $context, $fields));
    }
}