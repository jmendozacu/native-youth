<?php
/**
 * Copyright © 2016 MagestyApps. All rights reserved.
 *  * See COPYING.txt for license details.
 */

namespace MagestyApps\GeoipSwitcher\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use MagestyApps\GeoipSwitcher\Model\CurrencySwitcher;

class ManualSwitchCurrency implements ObserverInterface
{
    /**
     * @var CurrencySwitcher
     */
    private $currencySwitcher;

    /**
     * ManualSwitchCurrency constructor.
     * @param CurrencySwitcher $currencySwitcher
     */
    public function __construct(
        CurrencySwitcher $currencySwitcher
    ) {
        $this->currencySwitcher = $currencySwitcher;
    }

    /**
     * @param Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {
        $request = $observer->getEvent()->getRequest();
        if ($currency = (string) $request->getParam('currency')) {
            $this->currencySwitcher->setCurrencyCookie($currency);
        }

        return $this;
    }
}
