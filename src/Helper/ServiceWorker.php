<?php

/**
* Progressive Web App bundle for Contao Open Source CMS
*
* Copyright (C) 2019 pdir GmbH <https://pdir.de>
* @author  Mathias Arzberger <https://pdir.de>
*
* @license    https://opensource.org/licenses/lgpl-3.0.html
*/

namespace Pdir\PwaBundle\Helper; 

use Contao\PageModel;
use Contao\Helper;

/**
 * Class ServiceWorker
 *
 * @author Mathias Arzberger <https://pdir.de>
 */
class ServiceWorker
{
    /**
     * Root Url.
     *
     * @var string
     */
    protected $rootUrl;

    /**
     * Offline Page.
     *
     * @var string
     */
    protected $offlinePage;

    /**
     * Precached Pages.
     *
     * @var array
     */
    protected $preCachedPages = [];

    public function __construct($rootUrl, $offlinePage, $preCachedPages)
    {
        $this->rootUrl = $rootUrl;
        $this->offlinePage = $offlinePage;
        $this->preCachedPages = deserialize($preCachedPages);
    }

    /**
     * Get Root Url of
     */
    public function getRootUrl()
    {
        return $this->rootUrl;
    }

    /**
     * Get the list of URLs for external scripts to import into the service worker.
     *
     * @return string[]
     */
    public function getExternalScriptUrls()
    {
        $scripts = [
            $this->rootUrl . "bundles/pwa/js/vendor/workbox-sw.js",
        ];

        // @todo add google anylytics offline script

        // HOOK: add custom scripts into service worker
        if (isset($GLOBALS['TL_HOOKS']['addServiceWorkerScripts']) && \is_array($GLOBALS['TL_HOOKS']['addServiceWorkerScripts']))
        {
            foreach ($GLOBALS['TL_HOOKS']['addServiceWorkerScripts'] as $callback)
            {
                $this->import($callback[0]);
                $scripts = $this->{$callback[0]}->{$callback[1]}($scripts);
            }
        }

        return $scripts;
    }

    /**
     * Get the offline notification page URL.
     *
     * @return string
     */
    public function getOfflinePageUrl()
    {
        // @todo get offline page from root page
        return $this->rootUrl . 'offline.html';
    }

    /**
     * Get the path prefix for backend requests.
     *
     * @return string
     */
    public function getBackendPathPrefix()
    {
        // @todo get backend path from contao config
        return $this->rootUrl . 'contao';
    }

    /**
     * Get the configured paths with custom caching strategies.
     *
     * @return \array[]
     */
    public function getCustomStrategies()
    {
        $customStrategies = [];

        // @todo add customs strategies from root page config

        return json_encode($customStrategies);
    }

    /**
     * Check if offline Google Analytics features are enabled.
     *
     * @return bool
     */
    public function isGaOfflineEnabled()
    {
        return $this->pwaGaOfflineEnabled;
    }

    /**
     * Get html of other extension to append into the service worker.
     *
     * @return string
     */
    function getExtensionsHtml()
    {
        $html = null;

        // HOOK: add custom scripts into service worker
        if (isset($GLOBALS['TL_HOOKS']['addServiceWorkerExtensionHtml']) && \is_array($GLOBALS['TL_HOOKS']['addServiceWorkerExtensionHtml']))
        {
            foreach ($GLOBALS['TL_HOOKS']['addServiceWorkerExtensionHtml'] as $callback)
            {
                $this->import($callback[0]);
                $html .= $this->{$callback[0]}->{$callback[1]}($html);
            }
        }

        return $html;
    }


    /**
     * Get precached pages so they work offline
     */
    function getPreCachedPages()
    {
        $arrPages = [];

        // test precached resources
        $arrPages[] =  ['url', $this->rootUrl];
        $arrPages[] =  ['url', $this->rootUrl . 'files/contaodemo/theme/img/logo.png'];

        if(count($this->preCachedPages))
        {
            foreach($this->preCachedPages as $id)
            {
                $objPage = PageModel::findByPk($id);
                $arrPages[] =  ['url', $this->rootUrl . $objPage->getFrontendUrl()];
            }
        }

        // add offline page if set
        if($this->getOfflinePageUrl())
            $arrPages[] = ['url', $this->getOfflinePageUrl()];

        return json_encode($arrPages);
    }
}
