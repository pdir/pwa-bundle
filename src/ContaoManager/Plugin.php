<?php

/*
* This file is part of Contao.
*
* (c) Leo Feyer
*
* @license LGPL-3.0-or-later
*/

namespace Pdir\PwaBundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Pdir\PwaBundle\PwaBundle;

class Plugin implements BundlePluginInterface
{
    /**
     * {@inheritdoc}
     */
    public function getBundles(ParserInterface $parser): array
    {
        return [
            BundleConfig::create(PwaBundle::class)
                ->setLoadAfter([ContaoCoreBundle::class]),
        ];
    }
}