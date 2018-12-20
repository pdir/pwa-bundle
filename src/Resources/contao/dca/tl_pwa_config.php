<?php

/**
 * Progressive Web App bundle for Contao Open Source CMS
 *
 * Copyright (C) 2018 pdir GmbH <https://pdir.de>
 * @author  Mathias Arzberger <https://pdir.de>
 *
 * @license    https://opensource.org/licenses/lgpl-3.0.html
 */

$strTable = 'tl_pwa_config';

/**
 * tl_pwa_config
 */
$GLOBALS['TL_DCA'][$strTable] = [
    'config'   => [
        'dataContainer'     => 'Table',
        'enableVersioning'  => true,
        'onload_callback' => [
            ['Pdir\PwaBundle\EventListener\DataContainer\PageListener', 'updateManifest'],
            ['Pdir\PwaBundle\EventListener\DataContainer\PageListener', 'updateServiceWorker']
        ],
        'sql'               => [
            'keys' => [
                'id' => 'primary',
            ],
        ],
    ],
    'list'     => [
        'label'             => [
            'fields' => ['configTitle','manifestStartUrl'],
            'format' => '%s <span style="color:#999;padding-left:3px">(Manifest Url: %s)</span>',
        ],
        'sorting'           => [
            'mode'         => 1,
            'fields'       => ['configTitle'],
            'panelLayout'  => 'filter;search,limit',
        ],
        'global_operations' => [
            'all' => [
                'label'      => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href'       => 'act=select',
                'class'      => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset();"',
            ]
        ],
        'operations'        => [
            'edit' => [
                'label'           => &$GLOBALS['TL_LANG'][$strTable]['edit'],
                'href'            => 'act=edit',
                'icon'            => 'header.svg',
            ],
            'copy'   => [
                'label' => &$GLOBALS['TL_LANG'][$strTable]['copy'],
                'href'  => 'act=copy',
                'icon'  => 'copy.gif',
            ],
            'delete' => [
                'label'      => &$GLOBALS['TL_LANG'][$strTable]['delete'],
                'href'       => 'act=delete',
                'icon'       => 'delete.gif',
                'attributes' => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG'][$strTable]['deleteConfirm']
                    . '\'))return false;Backend.getScrollOffset()"',
            ],
            'show'   => [
                'label' => &$GLOBALS['TL_LANG'][$strTable]['show'],
                'href'  => 'act=show',
                'icon'  => 'show.gif',
            ]
        ],
    ],
    'palettes' => [
        'default' => '{general_legend},configTitle;'
            .'{manifest_legend},manifestAlias,manifestName,manifestShortName,manifestDescription,manifestBackgroundColor,manifestThemeColor,manifestOrientation,manifestDir,manifestDisplay,manifestIcons,manifestStartUrl;'
            .'{service_worker_legend},pwaExternalScripts,pwaFallbackImage,pwaImagesMaxAge,pwaImagesMaxEntries,pwaCustomStrategies,pwaOfflinePage,pwaGaOfflineEnabled,pwaPreCachedPages',
    ],
    'subpalettes' => [],
    'fields'   => [
        'id'        => [
            'sql' => "int(10) unsigned NOT NULL auto_increment",
        ],
        'tstamp'    => [
            'label' => &$GLOBALS['TL_LANG']['MSC']['tstamp'],
            'sql'   => "int(10) unsigned NOT NULL default '0'",
        ]
    ]
];

$GLOBALS['TL_DCA'][$strTable]['fields']['configTitle'] = array
(
    'label'                   => &$GLOBALS['TL_LANG'][$strTable]['configTitle'],
    'inputType'               => 'text',
    'eval' => array(
        'maxlength' => 255,
        'tl_class' => 'w50 clr',
    ),
    'sql'                     => "varchar(255) NOT NULL default ''",
);

$GLOBALS['TL_DCA'][$strTable]['fields']['manifestAlias'] = array
(
    'label'                   => &$GLOBALS['TL_LANG'][$strTable]['manifestAlias'],
    'exclude'                 => true,
    'search'                  => true,
    'inputType'               => 'text',
    'eval'                    => array('mandatory'=>true, 'unique'=>true, 'rgxp'=>'alnum', 'decodeEntities'=>true, 'maxlength'=>32, 'tl_class'=>'w50'),
    'save_callback' => array
    (
        array('Pdir\PwaBundle\EventListener\DataContainer\PwaConfigListener', 'checkManifestAlias')
    ),
    'sql'                     => "varchar(32) NOT NULL default ''",
);

$GLOBALS['TL_DCA'][$strTable]['fields']['manifestBackgroundColor'] = array(
    'label'                   => &$GLOBALS['TL_LANG'][$strTable]['manifestBackgroundColor'],
    'inputType'               => 'text',
    'eval'                    => array('maxlength'=>6, 'multiple'=>true, 'size'=>2, 'colorpicker'=>true, 'isHexColor'=>true, 'decodeEntities'=>true, 'tl_class'=>'w50 wizard'),
    'sql'                     => "varchar(64) NOT NULL default ''",
);

$GLOBALS['TL_DCA'][$strTable]['fields']['manifestDescription'] = array(
    'label'                   => &$GLOBALS['TL_LANG'][$strTable]['manifestDescription'],
    'inputType'               => 'textarea',
    'eval' => array(
        'style' => 'height:70px',
        'tl_class' => 'clr noresize',
    ),
    'sql'                     => "text NULL",
);

$GLOBALS['TL_DCA'][$strTable]['fields']['manifestDir'] = array(
    'label'                   => &$GLOBALS['TL_LANG'][$strTable]['manifestDir'],
    'inputType' => 'select',
    'options' => array(
        'auto',
        'ltr',
        'rtl',
    ),
    'reference' => &$GLOBALS['TL_LANG'][$strTable]['manifestDirLabel'],
    'eval' => array(
        'maxlength' => 4,
        'tl_class' => 'w50',
    ),
    'sql'                     => "varchar(4) NOT NULL default ''",
);

$GLOBALS['TL_DCA'][$strTable]['fields']['manifestDisplay'] = array(
    'label'                   => &$GLOBALS['TL_LANG'][$strTable]['manifestDisplay'],
    'inputType' => 'select',
    'options' => array(
        'standalone',
        'fullscreen',
        'minimal-ui',
        'browser',
    ),
    'reference' => &$GLOBALS['TL_LANG'][$strTable]['manifestDisplayLabel'],
    'eval' => array(
        'maxlength' => 14,
        'tl_class' => 'w50',
    ),
    'sql'                     => "varchar(14) NOT NULL default ''",
);

$GLOBALS['TL_DCA'][$strTable]['fields']['manifestIcons'] = array(
    'label'                   => &$GLOBALS['TL_LANG'][$strTable]['manifestIcons'],
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

$GLOBALS['TL_DCA'][$strTable]['fields']['manifestName'] = array
(
    'label'                   => &$GLOBALS['TL_LANG'][$strTable]['manifestName'],
    'inputType'               => 'text',
    'eval' => array(
        'maxlength' => 255,
        'tl_class' => 'w50 clr',
    ),
    'sql'                     => "varchar(255) NOT NULL default ''",
);


$GLOBALS['TL_DCA'][$strTable]['fields']['manifestOrientation'] = array(
    'label'                   => &$GLOBALS['TL_LANG'][$strTable]['manifestOrientation'],
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
    'reference'               => &$GLOBALS['TL_LANG'][$strTable]['manifestOrientationLabel'],
    'eval' => array(
        'maxlength' => 255,
        'tl_class' => 'w50 clr',
    ),
    'sql'                     => "varchar(32) NOT NULL default ''",
);

// @toDo add preferRelatedApplications
// @toDo add relatedApplications
// @toDo add scope

$GLOBALS['TL_DCA'][$strTable]['fields']['manifestShortName'] = array
(
    'label'                   => &$GLOBALS['TL_LANG'][$strTable]['manifestShortName'],
    'inputType'               => 'text',
    'eval' => array(
        'maxlength' => 255,
        'tl_class' => 'w50',
    ),
    'sql'                     => "varchar(32) NOT NULL default ''",
);

$GLOBALS['TL_DCA'][$strTable]['fields']['manifestStartUrl'] = array(
    'label'                   => &$GLOBALS['TL_LANG'][$strTable]['manifestStartUrl'],
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

$GLOBALS['TL_DCA'][$strTable]['fields']['manifestThemeColor'] = array(
    'label'                   => &$GLOBALS['TL_LANG'][$strTable]['manifestThemeColor'],
    'inputType'               => 'text',
    'eval'                    => array('maxlength'=>6, 'multiple'=>true, 'size'=>2, 'colorpicker'=>true, 'isHexColor'=>true, 'decodeEntities'=>true, 'tl_class'=>'w50 wizard'),
    'sql'                     => "varchar(64) NOT NULL default ''",
);

// 'pwaExternalScripts',

$GLOBALS['TL_DCA'][$strTable]['fields']['pwaFallbackImage'] = array(
    'label'                   => &$GLOBALS['TL_LANG'][$strTable]['pwaFallbackImage'],
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

// 'pwaImagesMaxAge',
// 'pwaImagesMaxEntries',
// 'pwaCustomStrategies',

$GLOBALS['TL_DCA'][$strTable]['fields']['pwaOfflinePage'] = array(
    'label'                   => &$GLOBALS['TL_LANG'][$strTable]['pwaOfflinePage'],
    'exclude'                 => true,
    'inputType'               => 'pageTree',
    'eval'                    => array('fieldType'=>'radio', 'tl_class'=>'clr'),
    'sql'                     => "blob NULL"
);

$GLOBALS['TL_DCA'][$strTable]['fields']['pwaPreCachedPages'] = array(
    'label'                   => &$GLOBALS['TL_LANG'][$strTable]['pwaPreCachedPages'],
    'exclude'                 => true,
    'inputType'               => 'pageTree',
    'eval'                    => array('fieldType'=>'checkbox', 'tl_class'=>'clr', 'multiple'=>true),
	'sql'                     => "blob NULL"
);
