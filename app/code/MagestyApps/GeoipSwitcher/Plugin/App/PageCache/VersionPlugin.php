<?php
/**
 * Copyright © 2016 MagestyApps. All rights reserved.
 *  * See COPYING.txt for license details.
 */

namespace MagestyApps\GeoipSwitcher\Plugin\App\PageCache;

use Magento\Store\Model\StoreManagerInterface;
use MagestyApps\GeoipSwitcher\Model\Blocker;
use MagestyApps\GeoipSwitcher\Model\CurrencySwitcher;
use MagestyApps\GeoipSwitcher\Model\StoreSwitcher;

class VersionPlugin
{
    /**
     * @var StoreSwitcher
     */
    private $storeSwitcher;

    /**
     * @var CurrencySwitcher
     */
    private $currencySwitcher;

    /**
     * @var Blocker
     */
    private $blocker;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * KernelPlugin constructor.
     * @param StoreSwitcher $storeSwitcher
     * @param CurrencySwitcher $currencySwitcher
     * @param Blocker $blocker
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        StoreSwitcher $storeSwitcher,
        CurrencySwitcher $currencySwitcher,
        Blocker $blocker,
        StoreManagerInterface $storeManager
    ) {
        $this->storeSwitcher = $storeSwitcher;
        $this->currencySwitcher = $currencySwitcher;
        $this->blocker = $blocker;
        $this->storeManager = $storeManager;
    }

    /**
     * Process store and currency switching before loading full page cache
     */
    public function beforeProcess()
    {
        $this->blocker->processBlocker();
        $this->storeSwitcher->processStoreSwitcher();
        $this->currencySwitcher->processCurrencySwitcher();
    }
}