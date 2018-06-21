<?php

/**
 * Progressive Web App bundle for Contao Open Source CMS
 *
 * Copyright (C) 2018 pdir GmbH <https://pdir.de>
 * @author  Mathias Arzberger <https://pdir.de>
 *
 * @license    https://opensource.org/licenses/lgpl-3.0.html
 */

/*
 * Register the hook.
 */
$GLOBALS['TL_HOOKS']['generatePage'][] = ['pwa.head.listener.hooks', 'generatePage'];