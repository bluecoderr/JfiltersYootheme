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

use Joomla\CMS\Router\SiteRouter;
use YOOtheme\Builder\Templates\TemplateHelper;
use YOOtheme\Config;
use Exception;

/**
 * @phpstan-import-type Template from TemplateHelper
 * @see templates/yootheme/packages/builder-joomla-source/src/Listener/LoadTemplateUrl.php
 * @since 3.0.0
 */
class LoadTemplateUrl
{
    public Config $config;
    public SiteRouter $router;

    public function __construct(Config $config, SiteRouter $router)
    {
        $this->config = $config;
        $this->router = $router;
    }

    /**
     *
     * @param array $template
     * @return Template
     * @since 3.0.0
     */
    public function handle(array $template): array
    {
        $url = '';

        try {
            switch ($template['type'] ?? '') {
                case 'com_jfilters.results':
                    $url = 'index.php?option=com_jfilters&view=results';
                    break;
            }
            if ($url) {
                $template['url'] = (string)$this->router->build($url);
            }
        } catch (Exception $e) {
            // ArticleHelper::query() throws exception if article "attribs" are invalid JSON
        }

        return $template;
    }
}