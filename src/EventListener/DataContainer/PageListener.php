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

    public $fieldPrefix = 'manifest';

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

        while ($objRoot->next()) {

            $arrManifest = $this->getManifestFieldsFromPageObj($objRoot->fetchAllAssoc()[0]);

            // remove alias
            unset($arrManifest['Alias']);

            // start url
            $startUrl = \PageModel::findByPk($arrManifest['StartUrl']);
            $arrManifest['StartUrl'] = $startUrl->getFrontendUrl() ? $startUrl->getFrontendUrl() : '/';

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

            $objFile = new \File(\StringUtil::stripRootDir(\System::getContainer()->getParameter('contao.web_dir')) . '/share/' . $objRoot->manifestAlias . '.webmanifest');
            $objFile->truncate();
            $objFile->append($manifestJson);
            $objFile->close();

            // Add a log entry
            \System::log('Generated manifest "' . $objRoot->manifestAlias . '.webmanifest"', __METHOD__, TL_CRON);
        }
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
        $varValue = \StringUtil::standardize($varValue); // see #5096

        // @todo implement check for existing alias

        return $varValue;
    }


    private function getManifestFieldsFromPageObj($arr)
    {
        $newArr = [];
        foreach($arr as $key => $value)
        {
            if(preg_match('/^' . $this->fieldPrefix . '/', $key))
            {
                $newArr[str_replace($this->fieldPrefix, '', $key)] = $value;
            }
        }

        return $newArr;
    }
}

