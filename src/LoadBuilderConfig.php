<?php
/**
 * @package     Bluecoder.JFilters
 *
 * @copyright   Copyright © 2024 - 2026 Blue-Coder.com. All rights reserved.
 * @license     GNU General Public License 2 or later, see COPYING.txt for license details.
 * @see: templates/yootheme/packages/builder-joomla-source/bootstrap.php
 */

namespace Bluecoder\Plugin\System\JfiltersYootheme;

\defined('_JEXEC') or die();

use Bluecoder\Component\Jfilters\Administrator\Field\MenuitemField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Multilanguage;
use YOOtheme\Builder\BuilderConfig;
use YOOtheme\Builder\Joomla\Source\UserHelper;

use function YOOtheme\trans;

class LoadBuilderConfig
{

    /**
     * Adds the template to the Customizer (YT4, YT5)
     *
     * @param BuilderConfig $config
     * @throws \Exception
     * @since 2.0.0
     * @see /templates/yootheme/packages/builder-joomla-source/src/Listener/LoadBuilderConfig.php
     */
    public static function handle($config)
    {
        $config->merge([
            'languages' => array_map(
                fn($lang) => [
                    'value' => $lang->value == '*' ? '' : strtolower($lang->value),
                    'text' => $lang->text,
                ],
                Multilanguage::isEnabled()
                    ? HTMLHelper::_('contentlanguage.existing', true, true)
                    : [],
            ),

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
     * @param BuilderConfig $config
     *
     * @return array[]
     * @throws \Exception
     * @since 2.0.0
     */
    protected static function getTemplates($config) : array
    {
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

        return [
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
                            'lang' => self::getLanguageField(),
                        ],
                    ],
                ],
            ],
        ];
    }

    protected static function getLanguageField(): array
    {
        return [
            'label' => trans('Limit by Language'),
            'type' => 'select',
            'defaultIndex' => 0,
            'options' => [['evaluate' => 'yootheme.builder.languages']],
            'show' => 'yootheme.builder.languages.length > 1 || lang',
        ];
    }
}