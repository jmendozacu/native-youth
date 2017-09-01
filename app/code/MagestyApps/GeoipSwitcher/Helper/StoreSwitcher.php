<?php
/**
 * Copyright © 2016 MagestyApps. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace MagestyApps\GeoipSwitcher\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class StoreSwitcher extends AbstractHelper
{
    const XML_PATH_ENABLED      = 'magestyapps_geoip/storeswitcher/enable';
    const XML_PATH_SWITCH_MODE  = 'magestyapps_geoip/storeswitcher/switch_mode';
    const XML_PATH_SWITCH_SCOPE = 'magestyapps_geoip/storeswitcher/switch_scope';
    const XML_PATH_SWITCH_TAX   = 'magestyapps_geoip/storeswitcher/switch_tax';
    const XML_PATH_EXCEPTION_URLS = 'magestyapps_geoip/storeswitcher/exception_urls';

    /**
     * Get "Automatically Switch Store" setting
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_ENABLED);
    }

    /**
     * Get store switcher mode
     *
     * @return mixed
     */
    public function getSwitchMode()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_SWITCH_MODE);
    }

    /**
     * Get store switcher scope
     *
     * @return mixed
     */
    public function getSwitchScope()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_SWITCH_SCOPE);
    }

    /**
     * Get store switcher scope
     *
     * @return mixed
     */
    public function getSwitchTax()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_SWITCH_TAX);
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
     * @return string
     */
    public function getCurrentUrl()
    {
        return $this->_urlBuilder->getCurrentUrl();
    }

    /**
     * Check whether current url should be excepted
     * from automatic store switching
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
