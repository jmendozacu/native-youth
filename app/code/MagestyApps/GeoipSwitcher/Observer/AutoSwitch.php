<?php
/**
 * Copyright © 2016 MagestyApps. All rights reserved.
 *  * See COPYING.txt for license details.
 */

namespace MagestyApps\GeoipSwitcher\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use MagestyApps\GeoipSwitcher\Model\Blocker;
use MagestyApps\GeoipSwitcher\Model\CurrencySwitcher;
use MagestyApps\GeoipSwitcher\Model\StoreSwitcher;

class AutoSwitch implements ObserverInterface
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
     * AutoSwitch constructor.
     * @param StoreSwitcher $storeSwitcher
     * @param CurrencySwitcher $currencySwitcher
     * @param Blocker $blocker
     */
    public function __construct(
        StoreSwitcher $storeSwitcher,
        CurrencySwitcher $currencySwitcher,
        Blocker $blocker
    ) {
        $this->storeSwitcher = $storeSwitcher;
        $this->currencySwitcher = $currencySwitcher;
        $this->blocker = $blocker;
    }

    /**
     * Automatically switch store based on visitor's location
     *
     * @param Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {
        $this->blocker->processBlocker();
        $this->storeSwitcher->processStoreSwitcher();
        $this->currencySwitcher->processCurrencySwitcher();

        return $this;
    }
}
