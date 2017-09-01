<?php
/**
 * Copyright © 2016 MagestyApps. All rights reserved.
 *  * See COPYING.txt for license details.
 */

namespace MagestyApps\GeoipSwitcher\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Blocker extends AbstractHelper
{
    const XML_PATH_ENABLED        = 'magestyapps_geoip/blocker/enable';
    const XML_PATH_COUNTRIES_LIST = 'magestyapps_geoip/blocker/countries';
    const XML_PATH_IP_LIST        = 'magestyapps_geoip/blocker/ip_list';
    const XML_PATH_REDIRECT_URL   = 'magestyapps_geoip/blocker/custom_redirect';

    /**
     * Check whether GeoIP Blocker is enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_ENABLED);
    }

    /**
     * Get list of countries to be blocked
     *
     * @return mixed
     */
    public function getCountriesList()
    {
        return explode(',', $this->scopeConfig->getValue(self::XML_PATH_COUNTRIES_LIST));
    }

    /**
     * Get list of IP addresses to be blocked
     *
     * @return mixed
     */
    public function getIpList()
    {
        $ipList = array_filter((array) preg_split('/\r?\n/', $this->scopeConfig->getValue(self::XML_PATH_IP_LIST)));

        foreach ($ipList as $k => $ip) {
            $ipList[$k] = trim($ip);
        }

        return $ipList;
    }

    /**
     * Get custom redirect url
     *
     * @return mixed
     */
    public function getRedirectUrl()
    {
        return trim($this->scopeConfig->getValue(self::XML_PATH_REDIRECT_URL));
    }
}
