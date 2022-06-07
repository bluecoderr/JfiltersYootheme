<?php
/**
 * The TemplateListener class as required by Yootheme Pro.
 * https://yootheme.com/support/yootheme-pro/joomla/developers-templates
 *
 * @package     Bluecoder.JFilters
 *
 * @copyright   Copyright © 2022 Blue-Coder.com. All rights reserved.
 * @license     GNU General Public License 2 or later, see COPYING.txt for license details.
 */

namespace Bluecoder\Plugin\System\JfiltersYootheme;

\defined('_JEXEC') or die();

use Joomla\CMS\Document\Document;

class TemplateListener
{
    public static function matchTemplate(Document $document, $view, $tpl)
    {
        if ($tpl) {
            return;
        }

        $layout = $view->getLayout();
        $context = $view->get('context');

        if ($context === 'com_jfilters.results') {
            $pagination = $view->get('pagination');
            $query = $view->get('query');

            return [
                'type' => $context,
                'query' => [
                    'pages' => $pagination->pagesCurrent === 1 ? 'first' : 'except_first',
                    'lang' => $document->language,
                ],
                'params' => [
                    'search' => [
                        'searchword' => $query->input ?: '',
                        'total' => $pagination->total,
                    ],
                    'results' => $view->get('Items'),
                    'pagination' => $pagination,
                ],
            ];
        }
    }
}