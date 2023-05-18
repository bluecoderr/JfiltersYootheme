<?php
/**
 * @package     Bluecoder.JFilters
 *
 * @copyright   Copyright © 2023 Blue-Coder.com. All rights reserved.
 * @license     GNU General Public License 2 or later, see COPYING.txt for license details.
 */

namespace Bluecoder\Plugin\System\JfiltersYootheme;

\defined('_JEXEC') or die();

use Bluecoder\Component\Jfilters\Administrator\Field\MenuitemField;
use Joomla\CMS\HTML\HTMLHelper;
use YOOtheme\Builder\Joomla\Fields\FieldsHelper;
use YOOtheme\Builder\Joomla\Source\UserHelper;
use YOOtheme\Config;
use YOOtheme\Builder\Joomla\Fields\Type;
use function YOOtheme\trans;

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
            if($key == 'JFiltersResultsItem') {
                $context = 'com_content.article';
                if ($fields = FieldsHelper::getFields($context)) {
                    static::configFields($source, $key, $context, $fields);
                }
            }
            $source->objectType(...$args);
        }
    }

    public static function initCustomizer(Config $config)
    {
        // Get the JFilters menu items
        $jfiltersPckXML = simplexml_load_file(JPATH_ADMINISTRATOR . '/components/com_jfilters/jfilters.xml');
        $newMenuItemOptions = [
            trans('All Items') => '',
        ];

        // We need the menu items, but returns an error below JFilters 1.9.1
        if ($jfiltersPckXML && $jfiltersPckXML->version && version_compare($jfiltersPckXML->version, '1.9.0', '>')) {
            $menuItemField = new MenuitemField();
            $menuItemField->component = 'com_jfilters';
            $menuItemField->clientId = 0;
            $menuItemOptions = $menuItemField->getOptions();
            foreach ($menuItemOptions as $menuItemOption) {
                $newMenuItemOptions[$menuItemOption->text] = $menuItemOption->value;
            }
        }

        $languageField = [
            'label' => trans('Limit by Language'),
            'type' => 'select',
            'defaultIndex' => 0,
            'options' => [['evaluate' => 'config.languages']],
            'show' => '$customizer.languages[\'length\'] > 2 || lang',
        ];

        $templates = [
            'com_jfilters.results' => [
                'label' => trans('JFilters Results'),
                'fieldset' => [
                    'default' => [
                        'fields' => [
                            'menuitem' => [
                                'label' => trans('Limit by Menu Item'),
                                'description' => trans(
                                    'The template is only assigned to the selected pages.'
                                ),
                                'type' => 'select',
                                'options' => $newMenuItemOptions
                            ],
                            'lang' => $languageField,
                        ],
                    ],
                ],
            ],
        ];

        $config->add('customizer.templates', $templates);

        $config->add(
            'customizer.categories',
            array_map(function ($category) {
                return ['value' => (string) $category->value, 'text' => $category->text];
            }, HTMLHelper::_('category.options', 'com_content'))
        );

        $config->add(
            'customizer.tags',
            array_map(function ($tag) {
                return ['value' => (string) $tag->value, 'text' => $tag->text];
            }, HTMLHelper::_('tag.options'))
        );

        $config->add(
            'customizer.authors',
            array_map(function ($user) {
                return ['value' => (string) $user->value, 'text' => $user->text];
            }, UserHelper::getAuthorList())
        );

        $config->add(
            'customizer.usergroups',
            array_map(function ($group) {
                return ['value' => (string) $group->value, 'text' => $group->text];
            }, HTMLHelper::_('user.groups'))
        );

        $config->add(
            'customizer.languages',
            array_map(function ($lang) {
                return [
                    'value' => $lang->value == '*' ? '' : strtolower($lang->value),
                    'text' => $lang->text,
                ];
            }, HTMLHelper::_('contentlanguage.existing', true, true))
        );
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
                        'metadata' => [
                            'label' => trans('Fields'),
                        ],
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