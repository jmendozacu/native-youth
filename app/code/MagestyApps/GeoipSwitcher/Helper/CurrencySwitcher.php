<?php
/**
 * Copyright © 2016 MagestyApps. All rights reserved.
 *  * See COPYING.txt for license details.
 */

namespace MagestyApps\GeoipSwitcher\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Helper\Context;

class CurrencySwitcher extends AbstractHelper
{
    const XML_PATH_ENABLE = 'magestyapps_geoip/currencyswitcher/enable';
    const XML_PATH_EXCEPTION_URLS = 'magestyapps_geoip/currencyswitcher/exception_urls';

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * CurrencySwitcher constructor.
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->storeManager = $storeManager;
    }

    /**
     * Check whether the auto-switcher is enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_ENABLE);
    }

    /**
     * Load the database of country-currency relations
     *
     * @return array|bool
     */
    public function getRelationsDatabase()
    {
        $path = BP . '/app/code/MagestyApps/GeoipSwitcher/etc/country-currency.csv';
        if (!file_exists($path)) {
            $path = BP . '/vendor/magestyapps/module-geoipswitcher/etc/country-currency.csv';
        }

        if (file_exists($path)) {
            return file($path);
        } else {
            return false;
        }
    }

    /**
     * Check whether the HTTP_USER_AGENT is a search bot
     *
     * @return bool
     */
    public function isCrawler()
    {
        $knownCrawlers = [
            'google',
            'msnbot',
            'rambler',
            'yahoo',
            'abachobOT',
            'accoona',
            'acoirobot',
            'aspseek',
            'crawler',
            'dumbot',
            'geonabot',
            'gigabot',
            'lycos',
            'msrbot',
            'scooter',
            'altavista',
            'idbot',
            'estyle',
            'scrubby',
            'facebook'
        ];

        $userAgent = strtolower($this->_httpHeader->getHttpUserAgent());

        $result = false;
        foreach ($knownCrawlers as $bot) {
            if (strpos($userAgent, $bot) !== false) {
                $result = true;
                break;
            }
        }

        return $result;
    }

    /**
     * Check whether current url should be excepted
     * from automatic currency switching
     *
     * @return bool
     */
    public function checkExceptionUrl()
    {
        $currentUrl = $this->_request->getRequestString();
        $exceptionUrls = explode("\n", $this->scopeConfig->getValue(self::XML_PATH_EXCEPTION_URLS));
        foreach ($exceptionUrls as $needle) {
            if (strpos($currentUrl, trim($needle)) !== false) {
                return false;
            }
        }

        return true;
    }
}
