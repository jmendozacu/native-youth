<?php
/**
 * Copyright © 2016 MagestyApps. All rights reserved.
 *  * See COPYING.txt for license details.
 */

namespace MagestyApps\GeoipSwitcher\Model;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Http\Context;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\Response\Http as HttpResponse;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Api\StoreCookieManagerInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use MagestyApps\GeoipSwitcher\Helper\StoreSwitcher as StoreSwitcherHelper;
use Magento\UrlRewrite\Model\UrlFinderInterface;
use Magento\Framework\Locale\ResolverInterface as Locale;
use Magento\Framework\View\DesignInterface;

class StoreSwitcher
{
    const SWITCH_MODE_ALWAYS   = 'always';
    const SWITCH_MODE_PARAM    = 'parameter';
    const SWITCH_MODE_ONETIME  = 'onetime';

    const SWITCH_SCOPE_GLOBAL  = 'global';
    const SWITCH_SCOPE_WEBSITE = 'website';
    const SWITCH_SCOPE_STORE   = 'store';

    const SESSION_STORE_CODE   = 'magestyappsgeoip_store';

    /**
     * @var StoreSwitcherHelper
     */
    private $helper;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var Http
     */
    private $request;

    /**
     * @var Geoip
     */
    private $geoip;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var UrlFinderInterface
     *
     */
    private $urlFinder;

    /**
     * @var HttpResponse
     */
    private $response;

    /**
     * @var StoreCookieManagerInterface
     */
    private $storeCookieManager;

    /**
     * @var Context
     */
    private $httpContext;

    /**
     * @var Locale
     */
    private $locale;

    /**
     * @var DesignInterface
     */
    private $design;

    /**
     * StoreSwitcher constructor.
     * @param StoreSwitcherHelper $helper
     * @param Session $session
     * @param Http $request
     * @param Geoip $geoip
     * @param StoreManagerInterface $storeManager
     * @param UrlFinderInterface $urlFinder
     * @param HttpResponse $response
     * @param StoreCookieManagerInterface $storeCookieManager
     * @param Context $httpContext
     * @param Locale $locale
     * @param DesignInterface $design
     */
    public function __construct(
        StoreSwitcherHelper $helper,
        Session $session,
        Http $request,
        Geoip $geoip,
        StoreManagerInterface $storeManager,
        UrlFinderInterface $urlFinder,
        HttpResponse $response,
        StoreCookieManagerInterface $storeCookieManager,
        Context $httpContext,
        Locale $locale,
        DesignInterface $design
    ) {
        $this->helper = $helper;
        $this->session = $session;
        $this->request = $request;
        $this->geoip = $geoip;
        $this->storeManager = $storeManager;
        $this->urlFinder = $urlFinder;
        $this->response = $response;
        $this->storeCookieManager = $storeCookieManager;
        $this->httpContext = $httpContext;
        $this->locale = $locale;
        $this->design = $design;
    }

    /**
     * Process store switcher
     *
     * @return $this
     */
    public function processStoreSwitcher()
    {
        if (!$this->canSwitch()) {
            return $this;
        }

        $currentStoreCode = $this->storeManager->getStore()->getCode();
        $customerStoreCode = $this->getCustomerStoreCode();

        if (!$customerStoreCode) {
            return $this;
        }

        if ($currentStoreCode != $customerStoreCode) {
            $this->switchStore($customerStoreCode);
        }

        return $this;
    }

    /**
     * Get requested store when switching via standard store view selector
     *
     * @return bool|mixed|null|string
     */
    public function getRequestedStoreCode()
    {
        $toStore = $this->request->getParam('___store');
        if ($toStore) {
            return $toStore;
        }

        $fromStore = $this->request->getParam('___from_store');
        if (!$fromStore || !($fromStoreModel = $this->storeManager->getStore($fromStore))) {
            return false;
        }

        $toStore = $this->request->getStoreCodeFromPath();

        return $toStore;
    }

    /**
     * Check whether the extension should switch store
     *
     * @return bool
     */
    public function canSwitch()
    {
        if (!$this->helper->isEnabled()) {
            return false;
        }

        $geoipStoreCode = $this->session->getData(self::SESSION_STORE_CODE);
        if ($this->helper->getSwitchMode() == self::SWITCH_MODE_ONETIME && $geoipStoreCode) {
            return false;
        }

        $requestStore = $this->getRequestedStoreCode();
        if ($this->helper->getSwitchMode() == self::SWITCH_MODE_PARAM && $requestStore) {
            $this->saveStoreCode($requestStore);
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
     * Get store code assigned to current customer's location
     *
     * @return bool
     */
    public function getCustomerStoreCode()
    {
        $geoipStoreCode = $this->session->getData(self::SESSION_STORE_CODE);
        if ($geoipStoreCode) {
            return $geoipStoreCode;
        }

        $defaultStore = $this->storeManager->getDefaultStoreView()->getCode();

        $location = $this->geoip->getCurrentLocation();
        if (!$location->getCountry()) {
            return $defaultStore;
        }

        $customerStoreCode = false;

        /** @var \Magento\Store\Model\ResourceModel\Store\Collection $allStores */
        $allStores = $this->storeManager->getStore()->getCollection();

        $scope = $this->helper->getSwitchScope();
        if ($scope == self::SWITCH_SCOPE_WEBSITE) {
            $websiteId = $this->storeManager->getWebsite()->getId();
            $allStores->addWebsiteFilter($websiteId);
        } elseif ($scope == self::SWITCH_SCOPE_STORE) {
            $storeGroupId = $this->storeManager->getGroup()->getId();
            $allStores->addGroupFilter($storeGroupId);
        }

        foreach ($allStores as $store) {
            if (!$store->getIsActive()) {
                continue;
            }
            $storeCountries = explode(',', $store->getStoreswitcherCountries());
            if (in_array($location->getCountry(), $storeCountries)) {
                $customerStoreCode = $store->getCode();
                break;
            }
        }

        if (!$customerStoreCode) {
            $customerStoreCode = $defaultStore;
        }

        return $customerStoreCode;
    }

    /**
     * Set cookies and/or session data to change current store
     *
     * @param $storeCode
     * @return $this
     */
    public function saveStoreCode($storeCode)
    {
        $this->storeManager->setCurrentStore($storeCode);

        $this->session->setData(self::SESSION_STORE_CODE, $storeCode);

        $store = $this->storeManager->getStore($storeCode);
        $this->storeCookieManager->setStoreCookie($store);

        return $this;
    }

    /**
     * Force switch design package when switching store-view
     *
     * @param $storeCode
     * @return $this
     */
    public function switchDesign($storeCode)
    {
        $storeTheme = $this->design->getConfigurationDesignTheme(null, ['store' => $storeCode]);
        $this->design->setDesignTheme($storeTheme);

        return $this;
    }

    /**
     * Force switch locale package when switching store-view
     *
     * @param $storeCode
     * @return $this
     */
    public function switchLocale($storeCode)
    {
        $storeId = $this->storeManager->getStore($storeCode)->getId();
        $this->locale->emulate($storeId);

        return $this;
    }

    /**
     * Switch store
     *
     * @param $storeCode
     * @return $this
     */
    public function switchStore($storeCode)
    {
        $fromStore = $this->storeManager->getStore();
        $toStore = $this->storeManager->getStore($storeCode);

        $defaultStoreView = $this->storeManager->getDefaultStoreView();
        $this->httpContext->setValue(Store::ENTITY, $storeCode, $defaultStoreView->getCode());
        $this->storeCookieManager->setStoreCookie($toStore);

        $redirectUrl = $this->getRedirectUrl($fromStore, $toStore);
        if ($redirectUrl) {
            $this->processRedirect($redirectUrl);
        }

        $this->saveStoreCode($storeCode);

        $this->switchDesign($storeCode);
        $this->switchLocale($storeCode);

        return $this;
    }

    /**
     * Send redirect headers
     *
     * @param $redirectUrl
     * @return $this
     */
    public function processRedirect($redirectUrl)
    {
        /**
         * Set current store code in cookies. Needed to prevent redirect
         * when visiting the redirected website for the second time
         */
//        $currentStoreCode = Mage::app()->getStore()->getCode();
//        $this->saveStoreCode($currentStoreCode);

        /**
         * Set redirect headers
         */
        $this->response->setRedirect($redirectUrl)->sendResponse();
        exit();
    }

    /**
     * Get current url but in the store to which customer should be redirected.
     *
     * @param StoreInterface $fromStore
     * @param StoreInterface $toStore
     * @return bool|string
     */
    public function getRedirectUrl(StoreInterface $fromStore, StoreInterface $toStore)
    {
        $requestPath = ltrim($this->request->getRequestString(), '/');
        $newRequestPath = '';

        if (!empty($requestPath)) {
            $rewrite = $this->urlFinder->findOneByData([
                'request_path' => $requestPath,
                'store_id' => $fromStore->getId()
            ]);

            if ($rewrite && $rewrite->getTargetPath()) {
                $targetPath = $rewrite->getTargetPath();
                $newRewrite = $this->urlFinder->findOneByData([
                    'target_path' => $targetPath,
                    'store_id' => $toStore->getId()
                ]);

                $newRequestPath = $newRewrite->getRequestPath();
            } else {
                $newRequestPath = $requestPath;
            }
        }

        /** @var Store $targetStore */
        $targetStore = $this->storeManager->getStore($toStore);
        $baseUrl = rtrim($targetStore->getBaseUrl(), '/');
        $redirectUrl = $baseUrl . '/' . $newRequestPath;

        $clearCurrentUrl = $this->clearUrl($this->helper->getCurrentUrl());
        $clearRedirectUrl = $this->clearUrl($redirectUrl);

        if ($clearCurrentUrl != $clearRedirectUrl) {
            return $redirectUrl;
        }

        return false;
    }

    /**
     * Remove not important data from url (like protocol, script name, port, etc.)
     *
     * @param $url
     * @return mixed|string
     */
    public function clearUrl($url)
    {
        $url = str_replace(['/index.php', 'http://', 'https://', ':80'], '', $url);

        $pos = strpos($url, '?');
        if ($pos !== false) {
            $url = substr($url, 0, $pos);
        }

        return $url;
    }
}
