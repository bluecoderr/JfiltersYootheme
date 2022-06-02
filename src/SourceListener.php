<?php
/**
 * @package     Bluecoder.JFilters
 *
 * @copyright   Copyright © 2022 Blue-Coder.com. All rights reserved.
 * @license     GNU General Public License 2 or later, see COPYING.txt for license details.
 */

namespace Bluecoder\Plugin\System\JfiltersYootheme;

\defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use YOOtheme\Builder\Joomla\Source\UserHelper;
use YOOtheme\Config;
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
            ['JFiltersResults', JfiltersType::config()],
            ['JFiltersResultsItem', JfiltersItemType::config()],
        ];

        foreach ($query as $args) {
            $source->queryType($args);
        }

        foreach ($types as $args) {
            $source->objectType(...$args);
        }
    }

    public static function initCustomizer(Config $config)
    {
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
}