<?php
/**
 * Copyright © 2016 MagestyApps. All rights reserved.
 *  * See COPYING.txt for license details.
 */

namespace MagestyApps\GeoipSwitcher\Model;

use MagestyApps\GeoipSwitcher\Helper\Blocker as Helper;
use MagestyApps\GeoipSwitcher\Helper\Database as GeoipHelper;
use Magento\Framework\App\Response\Http as HttpResponse;
use Magento\Framework\View\Asset\Repository as AssetRepo;

class Blocker
{
    /**
     * @var Geoip
     */
    private $geoip;

    /**
     * @var Helper
     */
    private $helper;

    /**
     * @var GeoipHelper
     */
    private $geoipHelper;

    /**
     * @var HttpResponse
     */
    private $httpResponse;

    /**
     * @var AssetRepo
     */
    private $assetRepo;

    /**
     * Blocker constructor.
     * @param Geoip $geoip
     * @param Helper $helper
     * @param GeoipHelper $geoipHelper
     * @param HttpResponse $httpResppnse
     * @param AssetRepo $assetRepo
     */
    public function __construct(
        Geoip $geoip,
        Helper $helper,
        GeoipHelper $geoipHelper,
        HttpResponse $httpResppnse,
        AssetRepo $assetRepo
    ) {
        $this->geoip = $geoip;
        $this->helper = $helper;
        $this->geoipHelper = $geoipHelper;
        $this->httpResponse = $httpResppnse;
        $this->assetRepo = $assetRepo;
    }

    /**
     * Process GeoIP blocker observer
     *
     * @return $this
     */
    public function processBlocker()
    {
        $this->blockRestrictedUsers();

        return $this;
    }

    /**
     * Check whether the visitors country is in blacklist
     *
     * @return bool
     */
    public function checkCountry()
    {
        $location = $this->geoip->getCurrentLocation();
        if (!$location->getCountry()) {
            return true;
        }

        $countries = $this->helper->getCountriesList();

        $isBlacklisted = in_array($location->getCountry(), $countries);

        return !$isBlacklisted;
    }

    /**
     * Check whether the visitors ip address is in blacklist
     *
     * @return bool
     */
    public function checkIp()
    {
        $customerIp = $this->geoipHelper->getCustomerIp();
        $ipList = $this->helper->getIpList();

        $isBlacklisted = in_array($customerIp, $ipList);

        return !$isBlacklisted;
    }

    /**
     * Block visitor if necessary
     *
     * @return $this
     */
    public function blockRestrictedUsers()
    {
        if (!$this->helper->isEnabled()) {
            return $this;
        }

        if ($this->checkIp() && $this->checkCountry()) {
            return $this;
        }

        if (empty($this->helper->getRedirectUrl())) {
            $this->renderAccessForbidden();
        } else {
            $this->httpResponse
                ->setRedirect($this->helper->getRedirectUrl())
                ->sendResponse();
        }

        return $this;
    }

    /**
     * Render default "403 Access Forbidden" error page
     */
    public function renderAccessForbidden()
    {
        $imgUtl = $this->assetRepo->getUrlWithParams('MagestyAppsgeoipSwitcher::images/403.png', []);

        $html = '<html>';
        $html .= '<head>';
        $html .= '<title>Access Forbidden!</title>';
        $html .= '</head>';
        $html .= '<body>';
        $html .= '<div style="';

        $html .= 'background: url('.$imgUtl.') no-repeat center;';
        $html .= 'background-size: 70%; width: 100%; height: 100%;';

        $html .= '"></div>';
        $html .= '</body>';
        $html .= '</html>';

        $this->httpResponse->setHttpResponseCode(403);
        $this->httpResponse->setBody($html);
        $this->httpResponse->sendResponse();
    }
}
