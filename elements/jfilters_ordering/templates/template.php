<?php

namespace YOOtheme;

use Bluecoder\Component\Jfilters\Site\Model\ResultsModel;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

$app = Factory::getApplication();
$app->getLanguage()->load('com_finder');

$component = $app->bootComponent('com_jfilters');
/** @var ResultsModel $model */
$model = $component->getMVCFactory()->createModel('Results', 'Site');

/** Needs to be called once, to populate state */
$model->getState();
$sortOrderFields = $model->getsortFields();

$activeField = array_values(array_filter($sortOrderFields, function ($sortOrderField) {
    return $sortOrderField->active;
}))[0];

$el = $this->el('div');

// Button
$button = $this->el('button', [

    'type' => 'button',

    'class' => [
        'uk-button uk-button-{button_style} [uk-button-{button_size} {@!button_style: text|link}]',
        'uk-flex-inline uk-flex-center uk-flex-middle' => $props['icon'] || $props['parent_icon'],
    ],

]);

// Icon + Parent Icon
if ($props['icon'] || $props['parent_icon']) {

    $icon = $this->el('span');

    if ($props['parent_icon']) {

        $icon->attr([
            'uk-drop-parent-icon' => true,
        ]);

        $props['icon'] = '';

    } else {

        $icon->attr([
            'class' => [
                'uk-margin-xsmall-right {@icon_align: left}',
                'uk-margin-xsmall-left {@icon_align: right}',
            ],

            'uk-icon' => $props['icon'],
        ]);

    }
}

// Dropdown
$dropdown = $this->el('div', [

    'uk-dropdown' => [
        'mode: click',
    ],

]);
?>

<?= $el($props, $attrs) ?>

<?= $button($props) ?>

<?php if ($props['icon'] && $props['icon_align'] == 'left') : ?>
    <?= $icon($props, '') ?>
<?php endif ?>

<?= Text::_('COM_FINDER_SORT_BY') ?>
<?= $activeField->label ?>

<?php if (($props['icon'] && $props['icon_align'] == 'right') || (!$props['icon'] && $props['parent_icon'])) : ?>
    <?= $icon($props, '') ?>
<?php endif ?>

<?= $button->end() ?>

<?= $dropdown() ?>
<ul class="uk-nav uk-dropdown-nav">
    <?php foreach ($sortOrderFields as $sortOrderField) : ?>
        <li<?= $sortOrderField->active ? ' class="uk-active"' : '' ?>>
            <a href="<?= Route::_($sortOrderField->url) ?>">
                <?= $sortOrderField->label ?>
            </a>
        </li>
    <?php endforeach; ?>
</ul>
<?= $dropdown->end() ?>

<?= $el->end() ?>
