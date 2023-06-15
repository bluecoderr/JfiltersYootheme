<?php
/**
 * The TemplateListener class as required by Yootheme Pro.
 * https://yootheme.com/support/yootheme-pro/joomla/developers-templates
 *
 * @package     Bluecoder.JFilters
 *
 * @copyright   Copyright © 2023 Blue-Coder.com. All rights reserved.
 * @license     GNU General Public License 2 or later, see COPYING.txt for license details.
 */

namespace Bluecoder\Plugin\System\JfiltersYootheme;

\defined('_JEXEC') or die();

use Joomla\CMS\Document\Document;
use Joomla\CMS\MVC\View\HtmlView;

class TemplateListener
{
    /**
     * Matches the templates with the currently loaded page
     *
     * @param   Document  $document
     * @param   HtmlView  $view
     * @param   string    $tpl
     *
     * @return array|void
     * @since 1.0.0
     * @see   /templates/yootheme/vendor/yootheme/builder-joomla-source/src/Listener/LoadTemplate.php
     *        (executed after the 'builder.template' event is triggered in the handle function
     * @compatibility YT3, YT4
     */
    public static function matchTemplate(Document $document, $view, $tpl)
    {
        if ($tpl) {
            return;
        }

        $context = $view->get('context');

        if ($context === 'com_jfilters.results') {
            $pagination = $view->get('pagination');
            $query = $view->get('query');
            $menuItemId = isset($view->menuItem) ? $view->menuItem->id : '';

            return [
                'type' => $context,
                'query' => [
                    'pages' => $pagination->pagesCurrent === 1 ? 'first' : 'except_first',
                    'lang' => $document->language,
                    'menuitem' => $menuItemId
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
        return null;
    }
}