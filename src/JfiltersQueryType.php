<?php
/**
 * @package     Bluecoder.JFilters
 *
 * @copyright   Copyright © 2022 Blue-Coder.com. All rights reserved.
 * @license     GNU General Public License 2 or later, see COPYING.txt for license details.
 */

namespace Bluecoder\Plugin\System\JfiltersYootheme;

\defined('_JEXEC') or die();

use function YOOtheme\trans;

class JfiltersQueryType
{
    /**
     * @return array
     */
    public static function config()
    {
        return [
            'fields' => [
                'jfiltersResults' => [
                    'type' => 'JFiltersResults',
                    'metadata' => [
                        'label' => trans('JFilters'),
                        'view' => ['com_jfilters.results'],
                        'group' => 'Page',
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::resolve',
                    ],
                ],
            ],
        ];
    }

    public static function resolve($root)
    {
        if (isset($root['results'])) {
            return $root['results'];
        }
    }
}