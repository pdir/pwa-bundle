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
use Contao\Environment;
use Contao\FrontendTemplate;
use Contao\PageModel;
use Pdir\PwaBundle\Helper\ServiceWorker;

class PageListener
{

    public $fieldPrefix = 'manifest';

    /**
     * Template.
     *
     * @var string
     */
    protected $strServiceWorkerTemplate = 'serviceworker.js';

    /**
     * Update manifest json in share folder
     *
     * @param DataContainer $dc
     */
    public function updateManifest(DataContainer $dc)
    {
        $objDatabase = \Database::getInstance();

        $objRoot = $objDatabase->prepare("SELECT * FROM tl_page WHERE id=?")
            ->limit(1)
            ->execute($dc->id);

        if ($objRoot->numRows < 1)
        {
            return;
        }

        while ($objRoot->next() && $objRoot->createManifest) {

            //echo "<br>Manifest<pre>"; print_r($objRoot->fetchAllAssoc()[0]); echo "</pre>";

            $arrManifest = $this->getManifestFieldsFromPageObj($objRoot->pwaConfig);

            echo "<br>Manifest<pre>"; print_r($arrManifest); echo "</pre>";

            // remove alias
            $strAlias = $arrManifest['Alias'];
            unset($arrManifest['Alias']);

            // start url
            $startUrl = \PageModel::findByPk($arrManifest['StartUrl']);
            $arrManifest['StartUrl'] = $startUrl !== null ? $startUrl->getFrontendUrl() : '/';

            // icons
            // @todo implement multiple icons
            $objFileModel = \FilesModel::findByUuid($arrManifest['Icons']);

            if ($objFileModel !== null) {
                $arrManifest['Icons'] = array(array(
                    "src" => $objFileModel->path ? $objFileModel->path : 'bundles/pwabundle/icon.png',
                    // "sizes" => @todo add sizes
                    // "type" => $objFileModel->type, @todo add image type
                ));
            }

            // colors
            $arrManifest['ThemeColor'] = unserialize($arrManifest['ThemeColor'])[0];
            $arrManifest['BackgroundColor'] = unserialize($arrManifest['BackgroundColor'])[0];

            // get lang from page
            $arrManifest['Lang'] = $objRoot->language;

            // get json from manifest generator
            $manifestJson = \System::getContainer()->get('contao.manifest.generator')->toJson($arrManifest);

            $objFile = new \File(\StringUtil::stripRootDir(\System::getContainer()->getParameter('contao.web_dir')) . '/share/' . $strAlias. '.webmanifest');
            $objFile->truncate();
            $objFile->append($manifestJson);
            $objFile->close();

            // Add a log entry
            \System::log('Generated manifest "' . $strAlias . '.webmanifest"', __METHOD__, TL_CRON);
        }
    }

    private function getManifestFieldsFromPageObj($id)
    {
        $objDatabase = \Database::getInstance();

        //echo "<br>PWAConfig: " . $id;

        $objPwaConfig = $objDatabase->prepare("SELECT * FROM tl_pwa_config WHERE id=?")
            ->limit(1)
            ->execute($id);

        if ($objPwaConfig->numRows < 1) {
            return;
        }

        while ($objPwaConfig->next()) {
            $arr = $objPwaConfig->fetchAllAssoc()[0];

            // echo "<br>objPwaConfig<pre>"; print_r($objPwaConfig); echo "</pre>";

            //echo "<br>while!";
            $newArr = [];
            foreach ($arr as $key => $value) {

                //echo "<br>Key:" . $key;
                //echo "<br>Val:" . $value;

                if (preg_match('/^' . $this->fieldPrefix . '/', $key)) {
                    $newArr[str_replace($this->fieldPrefix, '', $key)] = $value;
                }
            }

            //echo "<br>newArr<pre>"; print_r($newArr); echo "</pre>";

            return $newArr;
        }
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

            // echo "<pre>"; print_r($objTemplate); echo "</pre>";

            $strTemplate = $objTemplate->parse();

            $objFile = new \File(\StringUtil::stripRootDir(\System::getContainer()->getParameter('contao.web_dir')) . '/share/sw' . $objRoot->id . '.js');
            $objFile->truncate();
            $objFile->append($strTemplate);
            $objFile->close();

            // Add a log entry
            \System::log('Update ServiceWorker for ' . $strRootUrl, __METHOD__, TL_CONFIGURATION);
        }

    }
}

