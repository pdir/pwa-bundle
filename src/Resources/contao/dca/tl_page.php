<?php

/**
 * Progressive Web App bundle for Contao Open Source CMS
 *
 * Copyright (C) 2019 pdir GmbH <https://pdir.de>
 * @author  Mathias Arzberger <https://pdir.de>
 *
 * @license    https://opensource.org/licenses/lgpl-3.0.html
 */

/**
 * Callbacks
 */
$GLOBALS['TL_DCA']['tl_page']['config']['onload_callback'][] = ['Pdir\PwaBundle\EventListener\DataContainer\PageListener', 'updateManifest'];
$GLOBALS['TL_DCA']['tl_page']['config']['onload_callback'][] = ['Pdir\PwaBundle\EventListener\DataContainer\PageListener', 'updateServiceWorker'];

/**
 * Extend tl_page palettes
 */
Contao\CoreBundle\DataContainer\PaletteManipulator::create()
    ->addLegend('pwa_legend', 'sitemap_legend', \Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_AFTER, true)
    ->addField([
        'createManifest',
        'includeServiceWorker',
        'pwaConfig'
    ], 'pwa_legend', Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_AFTER)
    ->applyToPalette('root', 'tl_page');

/**
 * Add fields to tl_page
 */
$GLOBALS['TL_DCA']['tl_page']['fields']['createManifest'] = [

    'label' => &$GLOBALS['TL_LANG']['tl_page']['createManifest'],
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => ['submitOnChange' => true],
    'sql' => "char(1) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_page']['fields']['pwaConfig'] = [

    'label' => &$GLOBALS['TL_LANG']['tl_page']['pwaConfig'],
    'exclude' => true,
    'inputType' => 'select',
    'foreignKey' => "tl_pwa_config.manifestName",
    'eval' => ['tl_class' => 'w50 clr', 'includeBlankOption' => true],
    'sql' => "int(10) unsigned NOT NULL default '0'",
];

$GLOBALS['TL_DCA']['tl_page']['fields']['includeServiceWorker'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_page']['includeServiceWorker'],
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => ['submitOnChange' => true],
    'sql' => "char(1) NOT NULL default ''",
];
