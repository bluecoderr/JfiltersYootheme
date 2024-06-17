<?php
/**
 * @package     Bluecoder.JFilters
 *
 * @copyright   Copyright © 2024 Blue-Coder.com. All rights reserved.
 * @license     GNU General Public License 2 or later, see COPYING.txt for license details.
 */

namespace Bluecoder\Plugin\System\JfiltersYootheme;

\defined('_JEXEC') or die();

use Bluecoder\Component\Jfilters\Administrator\Field\MenuitemField;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use YOOtheme\Builder\BuilderConfig;
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
            $source->objectType(...$args);
        }
    }

    /**
     * Adds the template to the Customizer (YT3)
     *
     * @param   Config  $config
     *
     * @see templates/yootheme/vendor/yootheme/builder-joomla-source/src/SourceListener.php (YT3.x)
     *
     * @throws \Exception
     * @since 1.0.0
     */
    public static function initCustomizerYT3(Config $config)
    {
        
        $config->add('customizer.templates', self::getTemplates($config));

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

    /**
     * Adds the template to the Customizer (YT4)
     *
     * @param BuilderConfig $config
     * @throws \Exception
     * @since 2.0.0
     * @see templates/yootheme/vendor/yootheme/builder-joomla-source/src/Listener/LoadBuilderConfig.php (YT 4.x)
     */
    public static function initCustomizerYT4(BuilderConfig $config)
    {
        $languages = array_map(
            fn($lang) => [
                'value' => $lang->value == '*' ? '' : strtolower($lang->value),
                'text' => $lang->text,
            ],
            HTMLHelper::_('contentlanguage.existing', true, true)
        );

        $config->merge([
            'languages' => $languages,

            'templates' => self::getTemplates($config),

            'categories' => array_map(
                fn($category) => ['value' => (string) $category->value, 'text' => $category->text],
                HTMLHelper::_('category.options', 'com_content')
            ),

            'menus' => array_map(
                fn($menu) => ['value' => (string) $menu->value, 'text' => $menu->text],
                HTMLHelper::_('menu.menus', 'com_content')
            ),

            'tags' => array_map(
                fn($tag) => ['value' => (string) $tag->value, 'text' => $tag->text],
                HTMLHelper::_('tag.options')
            ),

            'authors' => array_map(
                fn($user) => ['value' => (string) $user->value, 'text' => $user->text],
                UserHelper::getAuthorList()
            ),

            'usergroups' => array_map(
                fn($group) => ['value' => (string) $group->value, 'text' => $group->text],
                HTMLHelper::_('user.groups')
            ),
        ]);
    }

    /**
     * Get the templates
     *
     * @param $config
     *
     * @return array[]
     * @throws \Exception
     * @since 2.0.0
     */
    protected static function getTemplates($config)
    {
        $YtMajorVersion = 3;
        if ($config instanceof BuilderConfig) {
            $YtMajorVersion = 4;
        }

        // Get the JFilters menu items
        $newMenuItemOptions = [];

        // We need the menu items, but getOptions is protected in versions lower to JFilters 1.9.1
        $menuItemField = new MenuitemField();
        if (is_callable([$menuItemField, 'getOptions'])) {
            $menuItemField->component = 'com_jfilters';
            $menuItemField->clientId = 0;
            $menuItemOptions = $menuItemField->getOptions();
            foreach ($menuItemOptions as $menuItemOption) {
                $newMenuItemOptions[$menuItemOption->text] = $menuItemOption->value;
            }
        }

        /*
         * Handle differences in syntax in various YT versions
         */
        $languageOptions = ['evaluate' => 'config.languages'];
        $languageShow = '$customizer.languages[\'length\'] > 2 || lang';

        // YT 4 uses different syntax for fetching the language options
        if (version_compare($YtMajorVersion, '3.99') == 1) {
            $languageOptions = ['evaluate' => 'yootheme.builder.languages'];
            $languageShow = 'yootheme.builder.languages.length > 2 || lang';
        }

        $languageField = [
            'label' => trans('Limit by Language'),
            'type' => 'select',
            'defaultIndex' => 0,
            'options' => [$languageOptions],
            'show' => $languageShow
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
                                'options' => $newMenuItemOptions,
                                'attrs' => [
                                    'multiple' => true,
                                    'class' => 'uk-height-small',
                                ],
                            ],
                            'lang' => $languageField,
                        ],
                    ],
                ],
            ],
        ];

        return $templates;
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