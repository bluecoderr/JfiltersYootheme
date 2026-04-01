<?php
/**
 * @package     Bluecoder.JFilters
 * https://yootheme.com/support/yootheme-pro/joomla/developers-templates
 * @copyright   Copyright © 2024 - 2026 Blue-Coder.com. All rights reserved.
 * @license     GNU General Public License 2 or later, see COPYING.txt for license details.
 */

namespace Bluecoder\Plugin\System\JfiltersYootheme;

\defined('_JEXEC') or die();

use Bluecoder\Component\Jfilters\Site\Model\ResultsModel;
use Joomla\CMS\Document\Document;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\Database\DatabaseDriver;
use YOOtheme\Builder\Joomla\Source\Listener\LoadTemplate;
use YOOtheme\Theme\Joomla\LoadTemplateEvent;

/**
 * @phpstan-import-type Template from LoadTemplate
 * @since 3.0.0
 */
class MatchTemplate
{
    public string $language;
    protected DatabaseDriver $db;

    public function __construct(?Document $document, DatabaseDriver $db)
    {
        $this->language = $document->language ?? 'en-gb';
        $this->db = $db;
    }

    /**
     * Matches the templates with the currently loaded page
     * @param LoadTemplateEvent $event
     * @return ?Template
     * @throws \Exception
     * @see   templates/yootheme/packages/builder-joomla-source/src/Listener/MatchTemplate.php:201
     *        (executed after the 'builder.template' event is triggered in the handle function
     * @compatibility YT4, YT5, YT5
     * @since 1.0.0
     */
    public function handle($event, $tpl = '') : ?array
    {
        //YTP4 passes the `HtmlView` as argument while YTP5 passes `LoadTemplateEvent`
        if ($event instanceof HtmlView) {
            if ($tpl) {
                return null;
            }
            $view = $event;
        } else {
            $view = $event->getView();
            if ($event->getTpl()) {
                return null;
            }
        }

        $context = method_exists($event, 'getContext') ? $event->getContext() : $view->get('context');
        /** @var ResultsModel $model */
        $model = $view->getModel();

        if ($context === 'com_jfilters.results') {
            $pagination = $model->getPagination();
            $query = $model->getQuery();
            $menuItem = Factory::getApplication()->getMenu()->getActive();
            $menuItemId = isset($menuItem) ? $menuItem->id : '';

            return [
                'type' => $context,
                'query' => [
                    'pages' => $pagination->pagesCurrent === 1 ? 'first' : 'except_first',
                    'lang' => $this->language,
                    'menuitem' => $menuItemId
                ],
                'params' => [
                    'search' => [
                        'searchword' => $query->input ?: '',
                        'total' => $pagination->total,
                    ],
                    'results' => $model->getItems(),
                    'pagination' => $pagination,
                ],
            ];
        }
        return null;
    }
}