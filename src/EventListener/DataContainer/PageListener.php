<?php

/**
 * Progressive Web App bundle for Contao Open Source CMS
 *
 * Copyright (C) 2018 pdir GmbH <https://pdir.de>
 * @author  Mathias Arzberger <https://pdir.de>
 *
 * @license    https://opensource.org/licenses/lgpl-3.0.html
 */

namespace Pdir\PwaBundle\EventListener\DataContainer;

use Contao\DataContainer;
use Contao\PageModel;

class PageListener
{
    /**
     * Update manifest json in share folder
     *
     * @param DataContainer $dc
     */
    public function updateManifest(DataContainer $dc)
    {
        // @todo implement manifest service
    }

    /**
     * Check the manifest alias
     *
     * @param mixed         $varValue
     * @param DataContainer $dc
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function checkManifestAlias($varValue, DataContainer $dc)
    {
        // No change or empty value
        if ($varValue == $dc->value || $varValue == '')
        {
            return $varValue;
        }
        $varValue = StringUtil::standardize($varValue); // see #5096

        // @todo implement check for existing alias

        return $varValue;
    }

}

