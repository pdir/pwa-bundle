<?php

/**
 * Progressive Web App bundle for Contao Open Source CMS
 *
 * Copyright (C) 2019 pdir GmbH <https://pdir.de>
 * @author  Mathias Arzberger <https://pdir.de>
 *
 * @license    https://opensource.org/licenses/lgpl-3.0.html
 */

namespace Pdir\PwaBundle\EventListener\DataContainer;

use Contao\DataContainer;
use Contao\Environment;
use Contao\FrontendTemplate;
use Contao\PageModel;
use Pdir\PwaBundle\Helper\ServiceWorker;

class PwaConfigListener
{

    public $fieldPrefix = 'manifest';

    /**
     * Template.
     *
     * @var string
     */
    protected $strServiceWorkerTemplate = 'serviceworker.js';

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
        $varValue = \StringUtil::standardize($varValue); // see #5096

        // @todo implement check for existing alias

        return $varValue;
    }

    /**
     * Update service worker javascript in share folder
     *
     * @param DataContainer $dc
     */
    public function updateServiceWorker(DataContainer $dc)
    {
        $objDatabase = \Database::getInstance();

        $objRoot = $objDatabase->prepare("SELECT * FROM tl_page WHERE id=?")
            ->limit(1)
            ->execute($dc->id);

        if ($objRoot->numRows < 1 && $objRoot->dns)
        {
            return;
        }

        while ($objRoot->next()) {

            if(!$objRoot->dns)
            {
                // Add a log entry
                \System::log('Update ServiceWorker fails: No Domain is set in Root Page', __METHOD__, TL_ERROR);
                return;
            }

            $strRootUrl = (Environment::get('ssl') ? 'https://' : 'http://') .  $objRoot->dns . TL_PATH . '/';

            $objTemplate = new FrontendTemplate(deserialize($this->strServiceWorkerTemplate));

            $objServiceWorker = new ServiceWorker($strRootUrl, $objRoot->pwaPreCachedPages, $objRoot->pwaOfflinePage);
            $objTemplate->rootUrl = $objServiceWorker->getRootUrl();
            $objTemplate->externalScriptUrls = $objServiceWorker->getExternalScriptUrls();
            $objTemplate->customStrategies = $objServiceWorker->getCustomStrategies();
            $objTemplate->backendPathPrefix = $objServiceWorker->getBackendPathPrefix();
            $objTemplate->extensionsHtml = $objServiceWorker->getExtensionsHtml();
            $objTemplate->preCachedPages = $objServiceWorker->getPreCachedPages();

            $strTemplate = $objTemplate->parse();

            $objFile = new \File(\StringUtil::stripRootDir(\System::getContainer()->getParameter('contao.web_dir')) . '/sw' . $objRoot->id . '.js');
            $objFile->truncate();
            $objFile->append($strTemplate);
            $objFile->close();

            // Add a log entry
            \System::log('Update ServiceWorker for ' . $strRootUrl, __METHOD__, TL_CONFIGURATION);
        }

    }
}

