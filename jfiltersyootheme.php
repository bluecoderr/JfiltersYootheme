<?php
/**
 * @package     Bluecoder.JFilters
 *
 * @copyright   Copyright © 2022 Blue-Coder.com. All rights reserved.
 * @license     GNU General Public License 2 or later, see COPYING.txt for license details.
 */

use Joomla\CMS\Plugin\CMSPlugin;
use YOOtheme\Application;

class plgSystemJfiltersyootheme extends CMSPlugin
{
    function onAfterInitialise()
    {
        if (!class_exists(Application::class, false)) {
            return;
        }
        $app = Application::getInstance();
        $app->load(__DIR__ . '/bootstrap.php');
    }
}