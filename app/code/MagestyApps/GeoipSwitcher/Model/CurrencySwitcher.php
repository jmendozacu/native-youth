<?php
/**
 * Copyright © 2016 MagestyApps. All rights reserved.
 *  * See COPYING.txt for license details.
 */

namespace MagestyApps\GeoipSwitcher\Model;

use Magento\Framework\Data\ObjectFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use MagestyApps\GeoipSwitcher\Helper\CurrencySwitcher as Helper;
use MagestyApps\GeoipSwitcher\Helper\Database;

class CurrencySwitcher
{
    const CURRENCY_COOKIE_CODE = 'magestyapps_currency_code';

    /**
     * @var Helper
     */
    private $helper;

    /**
     * @var Geoip
     */
    private $geoip;

    /**
     * @var Database
     */
    private $geoipHelper;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var CookieManagerInterface
     */
    private $cookieManager;

    /**
     * @var CookieMetadataFactory
     */
    private $cookieMetadataFactory;

    /**
     * CurrencySwitcher constructor.
     * @param Helper $helper
     * @param StoreManagerInterface $storeManager
     * @param CookieManagerInterface $cookieManager
     * @param CookieMetadataFactory $cookieMetadataFactory
     * @param Database $geoipHelper
     * @param Geoip $geoip
     */
    public function __construct(
        Helper $helper,
        StoreManagerInterface $storeManager,
        CookieManagerInterface $cookieManager,
        CookieMetadataFactory $cookieMetadataFactory,
        Database $geoipHelper,
        Geoip $geoip
    ) {
        $this->helper = $helper;
        $this->storeManager = $storeManager;
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->geoipHelper = $geoipHelper;
        $this->geoip = $geoip;
    }

    /**
     * Process currency switcher observer
     *
     * @return $this
     */
    public function processCurrencySwitcher()
    {
        if (!$this->canSwitch()) {
            return $this;
        }

        $location = $this->geoip->getCurrentLocation();
        $country = $location->getCountry();
        $currency = $this->getCurrencyByCountry($country);

        $this->switchCurrency($currency);

        return $this;
    }

    /**
     * Check whether the extension should switch currency automatically
     *
     * @return bool
     */
    public function canSwitch()
    {
        if (!$this->helper->isEnabled()) {
            return false;
        }

        if ($this->helper->isCrawler()) {
            return false;
        }

        if (!$this->helper->checkExceptionUrl()) {
            return false;
        }

        return true;
    }

    /**
     * Switches currency
     *
     * @param $geoipCurrency
     * @return $this
     */
    public function switchCurrency($geoipCurrency)
    {
        $currencyCookie = $this->cookieManager->getCookie(self::CURRENCY_COOKIE_CODE);
        $currency = ($currencyCookie) ? $currencyCookie : $geoipCurrency;

        /** @var Store $store */
        $store = $this->storeManager->getStore();
        if (!$currency || $store->getCurrentCurrencyCode() == $currency) {
            return $this;
        }

        $store->setCurrentCurrencyCode($currency);
        $this->setCurrencyCookie($currency);

        return $this;
    }

    /**
     * Set extension's currency cookie
     *
     * @param $currency
     * @return $this
     */
    public function setCurrencyCookie($currency)
    {
        $metadata = $this->cookieMetadataFactory->createPublicCookieMetadata()->setPath('/');
        $this->cookieManager->setPublicCookie(self::CURRENCY_COOKIE_CODE, $currency, $metadata);
        return $this;
    }

    /**
     * Get currency by country code
     *
     * @param $countryCode
     * @return string
     */
    public function getCurrencyByCountry($countryCode)
    {
        $curBase = $this->helper->getRelationsDatabase();

        if ($curBase === false || !count($curBase)) {
            return false;
        }

        /** @var Store $store */
        $store = $this->storeManager->getStore();
        $codes = $store->getAvailableCurrencyCodes(true);
        foreach ($curBase as $value) {
            $data = explode(';', $value);
            $curVal = trim($data[1]);

            $currentCountry = $this->geoipHelper->prepareCountryCode($countryCode);
            $compareCountry = $this->geoipHelper->prepareCountryCode($data[0]);

            if ($currentCountry != $compareCountry) {
                continue;
            }

            if (strstr($curVal, ',')) {
                $curCodes = explode(',', $curVal);
                if (!$curCodes) {
                    continue;
                }

                foreach ($curCodes as $code) {
                    $code = trim($code);
                    if (in_array($code, $codes)) {
                        return $code;
                    }
                }
            } else {
                if (in_array($curVal, $codes)) {
                    return $curVal;
                }
            }
        }

        return $store->getDefaultCurrencyCode();
    }
}
