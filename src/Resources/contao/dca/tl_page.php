<?php

/**
 * Progressive Web App bundle for Contao Open Source CMS
 *
 * Copyright (C) 2018 pdir GmbH <https://pdir.de>
 * @author  Mathias Arzberger <https://pdir.de>
 *
 * @license    https://opensource.org/licenses/lgpl-3.0.html
 */

/**
 * Callbacks
 */
$GLOBALS['TL_DCA']['tl_page']['config']['onload_callback'][] = ['Pdir\PwaBundle\EventListener\DataContainer\PageListener', 'updateManifest'];

/**
 * Extend tl_page palettes
 */
\Haste\Dca\PaletteManipulator::create()
    ->addLegend('manifest_legend', 'sitemap_legend', \Haste\Dca\PaletteManipulator::POSITION_AFTER)
    ->addField('createManifest', 'manifest_legend', \Haste\Dca\PaletteManipulator::POSITION_APPEND)
    ->addField(array(
        'manifestAlias',
        'manifestName',
        'manifestShortName',
        'manifestDescription',
        'manifestBackgroundColor',
        'manifestThemeColor',
        'manifestOrientation',
        'manifestDir',
        'manifestDisplay',
        'manifestIcon',
        'manifestStartUrl'
    ), 'manifest_legend', \Haste\Dca\PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('root', 'tl_page');

/**
 * Add a selector to tl_page
 */
$GLOBALS['TL_DCA']['tl_page']['palettes']['__selector__'][] = 'createManifest';

/**
 * Add fields to tl_page
 */
$GLOBALS['TL_DCA']['tl_page']['fields']['createManifest'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_page']['createManifest'],
    'exclude'                 => true,
    'inputType'               => 'checkbox',
    'eval'                    => array('submitOnChange'=>true),
    'sql'                     => "char(1) NOT NULL default ''",
);

$GLOBALS['TL_DCA']['tl_page']['fields']['manifestAlias'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_page']['manifestAlias'],
    'exclude'                 => true,
    'search'                  => true,
    'inputType'               => 'text',
    'eval'                    => array('mandatory'=>true, 'unique'=>true, 'rgxp'=>'alnum', 'decodeEntities'=>true, 'maxlength'=>32, 'tl_class'=>'w50'),
    'save_callback' => array
    (
        array('Pdir\PwaBundle\EventListener\DataContainer\PageListener', 'checkManifestAlias')
    ),
    'sql'                     => "varchar(32) NOT NULL default ''",
);

$GLOBALS['TL_DCA']['tl_page']['fields']['manifestBackgroundColor'] = array(
    'label'                   => &$GLOBALS['TL_LANG']['tl_page']['manifestBackgroundColor'],
    'inputType'               => 'text',
    'eval'                    => array('maxlength'=>6, 'multiple'=>true, 'size'=>2, 'colorpicker'=>true, 'isHexColor'=>true, 'decodeEntities'=>true, 'tl_class'=>'w50 wizard'),
    'sql'                     => "varchar(64) NOT NULL default ''",
);

$GLOBALS['TL_DCA']['tl_page']['fields']['manifestDescription'] = array(
    'label'                   => &$GLOBALS['TL_LANG']['tl_page']['manifestDescription'],
    'inputType'               => 'textarea',
    'eval' => array(
        'style' => 'height:70px',
        'tl_class' => 'clr noresize',
    ),
    'sql'                     => "text NULL",
);

$GLOBALS['TL_DCA']['tl_page']['fields']['manifestDir'] = array(
    'label'                   => &$GLOBALS['TL_LANG']['tl_page']['manifestDir'],
    'inputType' => 'select',
    'options' => array(
        'auto',
        'ltr',
        'rtl',
    ),
    'reference' => &$GLOBALS['TL_LANG']['tl_page']['manifestDirLabel'],
    'eval' => array(
        'maxlength' => 4,
        'tl_class' => 'w50',
    ),
    'sql'                     => "varchar(4) NOT NULL default ''",
);

$GLOBALS['TL_DCA']['tl_page']['fields']['manifestDisplay'] = array(
    'label'                   => &$GLOBALS['TL_LANG']['tl_page']['manifestDisplay'],
    'inputType' => 'select',
    'options' => array(
        'standalone',
        'fullscreen',
        'minimal-ui',
        'browser',
    ),
    'reference' => &$GLOBALS['TL_LANG']['tl_page']['manifestDisplayLabel'],
    'eval' => array(
        'maxlength' => 14,
        'tl_class' => 'w50',
    ),
    'sql'                     => "varchar(14) NOT NULL default ''",
);

$GLOBALS['TL_DCA']['tl_page']['fields']['manifestIcon'] = array(
    'label'                   => &$GLOBALS['TL_LANG']['tl_page']['manifestIcon'],
    'inputType' => 'fileTree',
    'eval' => array(
        'files' => true,
        'filesOnly' => true,
        'extensions' => 'jpg,png,gif,svg',
        'fieldType' => 'radio',
        'tl_class' => 'clr',
    ),
    'sql'                     => "blob NULL",
);

// lang is used from root page

$GLOBALS['TL_DCA']['tl_page']['fields']['manifestName'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_page']['manifestName'],
    'inputType'               => 'text',
    'eval' => array(
        'maxlength' => 255,
        'tl_class' => 'w50 clr',
    ),
    'sql'                     => "varchar(255) NOT NULL default ''",
);


$GLOBALS['TL_DCA']['tl_page']['fields']['manifestOrientation'] = array(
    'label'                   => &$GLOBALS['TL_LANG']['tl_page']['manifestOrientation'],
    'inputType' => 'select',
    'options' => array(
        'any',
        'natural',
        'portrait',
        'portrait-primary',
        'portrait-secondary',
        'landscape',
        'landscape-primary',
        'landscape-secondary',
    ),
    'reference'               => &$GLOBALS['TL_LANG']['tl_page']['manifestOrientationLabel'],
    'eval' => array(
        'maxlength' => 255,
        'tl_class' => 'w50 clr',
    ),
    'sql'                     => "varchar(32) NOT NULL default ''",
);

// @toDo add preferRelatedApplications
// @toDo add relatedApplications
// @toDo add scope

$GLOBALS['TL_DCA']['tl_page']['fields']['manifestShortName'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_page']['manifestShortName'],
    'inputType'               => 'text',
    'eval' => array(
        'maxlength' => 255,
        'tl_class' => 'w50',
    ),
    'sql'                     => "varchar(32) NOT NULL default ''",
);

$GLOBALS['TL_DCA']['tl_page']['fields']['manifestStartUrl'] = array(
    'label'                   => &$GLOBALS['TL_LANG']['tl_page']['manifestStartUrl'],
    'inputType'               => 'pageTree',
    'foreignKey'              => 'tl_page.title',
    'relation' => array(
        'type' => 'hasOne',
        'load' => 'lazy'
    ),
    'eval' => array(
        'fieldType'=>'radio',
        'tl_class' => 'clr',
    ),
    'sql'                     => "varchar(255) NOT NULL default ''",
);

$GLOBALS['TL_DCA']['tl_page']['fields']['manifestThemeColor'] = array(
    'label'                   => &$GLOBALS['TL_LANG']['tl_page']['manifestThemeColor'],
    'inputType'               => 'text',
    'eval'                    => array('maxlength'=>6, 'multiple'=>true, 'size'=>2, 'colorpicker'=>true, 'isHexColor'=>true, 'decodeEntities'=>true, 'tl_class'=>'w50 wizard'),
    'sql'                     => "varchar(64) NOT NULL default ''",
);