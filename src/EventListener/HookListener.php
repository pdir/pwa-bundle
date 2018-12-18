<?php

/**
 * Progressive Web App bundle for Contao Open Source CMS
 *
 * Copyright (C) 2018 pdir GmbH <https://pdir.de>
 * @author  Mathias Arzberger <https://pdir.de>
 *
 * @license    https://opensource.org/licenses/lgpl-3.0.html
 */

namespace Pdir\PwaBundle\EventListener;

use Contao\CoreBundle\Framework\ContaoFrameworkInterface;
use Contao\LayoutModel;
use Contao\PageModel;
use Contao\PageRegular;

class HookListener
{
    /**
     * @var ContaoFrameworkInterface
     */
    private $framework;

    /**
     * Constructor.
     *
     * @param ContaoFrameworkInterface $framework
     */
    public function __construct(ContaoFrameworkInterface $framework)
    {
        $this->framework = $framework;
    }

    /**
     * Modify the page object.
     *
     * @param PageModel   $page
     * @param LayoutModel $layout
     * @param PageRegular $pageRegular
     */
    public function generatePage(PageModel $page, LayoutModel $layout, PageRegular $pageRegular)
    {
        /*
         * @var $objPage \Contao\PageModel
         */
        global $objPage;

        $rootPage = $this->framework->getAdapter(PageModel::class)->findByPk($objPage->rootId ?: $objPage->id);

        if($rootPage->createManifest)
        {
            $GLOBALS['TL_HEAD'][] = '<link rel="manifest" href="share/' . $rootPage->manifestAlias . '.webmanifest" />';
        }

        if($rootPage->includeServiceWorker)
        {
            $GLOBALS['TL_HEAD'][] = <<<EOF
<script>
  if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('share/sw{$rootPage->id }.js', {scope: '/'})
      .then(() => console.log('service worker installed'))
      .catch(err => console.error('Error', err));
  }
  </script>
EOF;
        }
    }

}
