<?php

/**
 * Progressive Web App bundle for Contao Open Source CMS
 *
 * Copyright (C) 2019 pdir GmbH <https://pdir.de>
 * @author  Mathias Arzberger <https://pdir.de>
 *
 * @license    https://opensource.org/licenses/lgpl-3.0.html
 */

$assetsDir = 'bundles/pdirpwabundle';

/**
 * Add backend modules
 */
if (!is_array($GLOBALS['BE_MOD']['pdir'])) {
    array_insert($GLOBALS['BE_MOD'], 1, array('pdir' => array()));
}

$GLOBALS['BE_MOD']['pdir']['pwa'] = [
    'tables' => ['tl_pwa_config'],
    'stylesheet' => $assetsDir . '/css/backend.css'
];

/*
 * Register the hook.
 */
$GLOBALS['TL_HOOKS']['generatePage'][] = ['pwa.head.listener.hooks', 'generatePage'];

