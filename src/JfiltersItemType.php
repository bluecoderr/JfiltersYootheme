<?php
/**
 * @package     Bluecoder.JFilters
 *
 * @copyright   Copyright © 2022 Blue-Coder.com. All rights reserved.
 * @license     GNU General Public License 2 or later, see COPYING.txt for license details.
 */

namespace Bluecoder\Plugin\System\JfiltersYootheme;

\defined('_JEXEC') or die();

use Joomla\CMS\Categories\Categories;
use Joomla\CMS\Categories\CategoryNode;
use Joomla\CMS\Factory;
use Joomla\CMS\User\User;
use Joomla\Component\Finder\Administrator\Indexer\Result;
use Joomla\String\StringHelper;
use Joomla\CMS\HTML\HTMLHelper;
use YOOtheme\Builder\Joomla\Source\Type\ArticleType;
use YOOtheme\Path;
use YOOtheme\View;

use function YOOtheme\app;
use function YOOtheme\trans;

class JfiltersItemType
{
    /**
     * @return array
     */
    public static function config()
    {
        return [
            'fields' => [
                'title' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Title'),
                        'filters' => ['limit'],
                    ],
                ],
                'description' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Content'),
                        'filters' => ['limit'],
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::description',
                    ],
                ],

                'publish_start_date' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Published'),
                        'filters' => ['date'],
                    ],
                ],

                'metaString' => [
                    'type' => 'String',
                    'args' => [
                        'format' => [
                            'type' => 'String',
                        ],
                        'separator' => [
                            'type' => 'String',
                        ],
                        'link_style' => [
                            'type' => 'String',
                        ],
                        'show_publish_date' => [
                            'type' => 'Boolean',
                        ],
                        'show_author' => [
                            'type' => 'Boolean',
                        ],
                        'show_taxonomy' => [
                            'type' => 'String',
                        ],
                        'parent_id' => [
                            'type' => 'String',
                        ],
                        'date_format' => [
                            'type' => 'String',
                        ],
                    ],
                    'metadata' => [
                        'label' => trans('Meta'),
                        'arguments' => [
                            'format' => [
                                'label' => trans('Format'),
                                'description' => trans(
                                    'Display the meta text in a sentence or a horizontal list.'
                                ),
                                'type' => 'select',
                                'default' => 'list',
                                'options' => [
                                    trans('List') => 'list',
                                    trans('Sentence') => 'sentence',
                                ],
                            ],
                            'separator' => [
                                'label' => trans('Separator'),
                                'description' => trans('Set the separator between fields.'),
                                'default' => '|',
                                'enable' => 'arguments.format === "list"',
                            ],
                            'link_style' => [
                                'label' => trans('Link Style'),
                                'description' => trans('Set the link style.'),
                                'type' => 'select',
                                'default' => '',
                                'options' => [
                                    'Default' => '',
                                    'Muted' => 'link-muted',
                                    'Text' => 'link-text',
                                    'Heading' => 'link-heading',
                                    'Reset' => 'link-reset',
                                ],
                            ],
                            'show_publish_date' => [
                                'label' => trans('Display'),
                                'description' => trans('Show or hide fields in the meta text.'),
                                'type' => 'checkbox',
                                'default' => true,
                                'text' => trans('Show date'),
                            ],
                            'show_author' => [
                                'type' => 'checkbox',
                                'default' => true,
                                'text' => trans('Show author'),
                            ],
                            'show_taxonomy' => [
                                'type' => 'select',
                                'default' => 'category',
                                'options' => [
                                    trans('Hide Term List') => '',
                                    trans('Show Category') => 'category',
                                    trans('Show Tags') => 'tag',
                                ],
                            ],
                            'parent_id' => [
                                'label' => trans('Parent Tag'),
                                'description' => trans(
                                    'Tags are only loaded from the selected parent tag.'
                                ),
                                'type' => 'select',
                                'default' => '0',
                                'show' => 'arguments.show_taxonomy === "tag"',
                                'options' => [
                                    ['value' => '0', 'text' => 'Root'],
                                    ['evaluate' => 'config.tags'],
                                ],
                            ],
                            'date_format' => [
                                'label' => trans('Date Format'),
                                'description' => trans(
                                    'Select a predefined date format or enter a custom format.'
                                ),
                                'type' => 'data-list',
                                'default' => '',
                                'options' => [
                                    'Aug 6, 1999 (M j, Y)' => 'M j, Y',
                                    'August 06, 1999 (F d, Y)' => 'F d, Y',
                                    '08/06/1999 (m/d/Y)' => 'm/d/Y',
                                    '08.06.1999 (m.d.Y)' => 'm.d.Y',
                                    '6 Aug, 1999 (j M, Y)' => 'j M, Y',
                                    'Tuesday, Aug 06 (l, M d)' => 'l, M d',
                                ],
                                'enable' => 'arguments.show_publish_date',
                                'attrs' => [
                                    'placeholder' => 'Default',
                                ],
                            ],
                        ],
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::metaString',
                    ],
                ],

                'images' => [
                    'type' => 'ArticleImages',
                    'metadata' => [
                        'label' => '',
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::images',
                    ],
                ],

                'category' => [
                    'type' => 'Category',
                    'metadata' => [
                        'label' => trans('Category'),
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::category',
                    ],
                ],

                'author' => [
                    'type' => 'User',
                    'metadata' => [
                        'label' => trans('Author'),
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::author',
                    ],
                ],

                'route' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Link'),
                    ],
                ],
            ],

            'metadata' => [
                'type' => true,
                'label' => trans('JFilters Results Item'),
            ],
        ];
    }

    /**
     * Truncates the description based on the menu item/component setting.
     *
     * @param   Result $item
     * @param   array  $args
     *
     * @return mixed
     * @throws \Exception
     * @since 1.0.0
     */
    public static function description($item, array $args)
    {
        $app = Factory::getApplication();
        $params = $app->getParams();
        $desc_length = $params->get('description_length', 255);
        $query = $app->getInput()->get('q');
        $term_length = StringHelper::strlen($query);
        $pad_length = $term_length < $desc_length ? (int)floor(($desc_length - $term_length) / 2) : 0;

        // Make sure we highlight term both in introtext and fulltext
        $full_description = $item->description;
        if (!empty($item->summary) && !empty($item->body)) {
            $full_description = Helper::parse($item->summary . $item->body);
        }

        // Find the position of the search term
        $pos = $term_length ? StringHelper::strpos(StringHelper::strtolower($full_description),
            StringHelper::strtolower($query)) : false;

        // Find a potential start point
        $start = ($pos && $pos > $pad_length) ? $pos - $pad_length : 0;

        // Find a space between $start and $pos, start right after it.
        $space = StringHelper::strpos($full_description, ' ', $start > 0 ? $start - 1 : 0);
        $start = ($space && $space < $pos) ? $space + 1 : $start;

        $description = HTMLHelper::_('string.truncate', StringHelper::substr($full_description, $start), $desc_length,
            true);

        return $description;
    }

    /**
     * @param Result $item
     *
     * @return string
     */
    public static function metaString($item, array $args)
    {
        $args += [
            'format' => 'list',
            'separator' => '|',
            'link_style' => '',
            'show_publish_date' => true,
            'show_author' => true,
            'show_taxonomy' => 'category',
            'date_format' => '',
        ];

        $props = [
            'id',
            'author',
            'created_by',
            'created_by_alias',
            'contact_link',
            'catid',
            'category' => 'category_title',
        ];

        $article = new \stdClass();
        foreach ($props as $field => $prop) {
            if (is_numeric($field)) {
                $article->$prop = $item->getElement($prop);
            } else {
                $article->$prop = $item->getElement($field);
            }
        }

        $article->publish_up = $item->publish_start_date;
        $tags = $args['show_taxonomy'] === 'tag' ? ArticleType::tags($article, $args) : null;
        return app(View::class)->render(
            Path::get('../../../../templates/yootheme/vendor/yootheme/builder-joomla-source/templates/meta'),
            compact('article', 'tags', 'args')
        );
    }

    /**
     * @param Result $item
     *
     * @return array
     */
    public static function images($item)
    {
        return json_decode($item->getElement('images'));
    }

    /**
     * @param Result $item
     *
     * @return CategoryNode|null
     */
    public static function category($item)
    {
        $id = $item->getElement('catid');
        return $id ? Categories::getInstance('content', ['countItems' => true])->get($id) : null;
    }

    /**
     * @param Result $item
     *
     * @return User
     */
    public static function author($item)
    {
        $user = Factory::getUser($item->getElement('created_by'));

        if ($user && $item->getElement('created_by_alias')) {
            $user = clone $user;
            $user->name = $item->getElement('created_by_alias');
        }

        return $user;
    }
}