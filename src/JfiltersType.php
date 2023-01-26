<?php
/**
 * @package     Bluecoder.JFilters
 *
 * @copyright   Copyright © 2023 Blue-Coder.com. All rights reserved.
 * @license     GNU General Public License 2 or later, see COPYING.txt for license details.
 */

namespace Bluecoder\Plugin\System\JfiltersYootheme;

\defined('_JEXEC') or die();

use function YOOtheme\trans;

class JfiltersType
{
    /**
     * @return array
     */
    public static function config()
    {
        return [
            'fields' => [
                'searchword' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Search Word'),
                    ],
                ],

                'total' => [
                    'type' => 'Int',
                    'metadata' => [
                        'label' => trans('Item Count'),
                    ],
                ],
            ],

            'metadata' => [
                'type' => true,
                'label' => trans('JFilters Results'),
            ],
        ];
    }
}