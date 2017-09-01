<?php
/**
 * Celebros
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish correct extension functionality.
 * If you wish to customize it, please contact Celebros.
 *
 ******************************************************************************
 * @category    Celebros
 * @package     Celebros_ConversionPro
 */
namespace Celebros\ConversionPro\Helper;

use \Magento\Store\Model\ScopeInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_PATH_ENABLED                = 'conversionpro/general_settings/enabled';
    const XML_PATH_HOST                   = 'conversionpro/general_settings/host';
    const XML_PATH_SITE_KEY               = 'conversionpro/general_settings/sitekey';
    const XML_PATH_ADD_DIV                = 'conversionpro/general_settings/adddiv';
    const XML_PATH_ADD_SCRIPTS            = 'conversionpro/general_settings/addscripts';
    const XML_PATH_HIDE_CONTENT           = 'conversionpro/general_settings/hidecontent';
    const XML_PATH_NAV_TO_SEARCH          = 'conversionpro/nav_to_search_settings/nav_to_search';
    const XML_PATH_NAV_TO_SEARCH_ENABLE_BLACKLIST =
        'conversionpro/nav_to_search_settings/nav_to_search_enable_blacklist';
    const XML_PATH_NAV_TO_SEARCH_BLACKLIST =
        'conversionpro/nav_to_search_settings/nav_to_search_blacklist';
    const XML_PATH_SCRIPT_PATH            = 'conversionpro/advanced/scripts_path';
    const XML_PATH_CLIENT_CONFIG_PATH     = 'conversionpro/advanced/client_config_path';
    const XML_PATH_CLIENT_CONFIG_FILENAME = 'conversionpro/advanced/client_config_js_filename';
    
    const PRODUCT_LIST_CONTAINER_ID = 'celUITDiv';
    
    public function isCategoryIdBlacklisted($categoryId, $storeId = null)
    {
        return $this->getNavToSearchEnableBlacklist($storeId)
            && in_array($categoryId, $this->getNavToSearchBlacklist($storeId));
    }
    
    public function getProductListContainerId()
    {
        return self::PRODUCT_LIST_CONTAINER_ID;
    }
    
    public function getExternalJsUrls()
    {
        $protocol = $this->_getRequest()->isSecure() ? 'https' : 'http';
        $jqueryUrl = $protocol . '://'
            . implode('/', [$this->getHost(), $this->getScriptPath(), $this->getJqueryFilename()]);
        $clientConfigUrl = $protocol . '://'
            . implode('/', [$this->getHost(), $this->getClientConfigPath(), $this->getSiteKey(), 'output', $this->getClientConfigFilename()]);
        return [$jqueryUrl, $clientConfigUrl];
    }
    
    public function isEnabledOnFrontend($store = null)
    {
        return $this->isModuleOutputEnabled() && $this->isEnabled($store);
    }
    
    public function isEnabled($store = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLED, ScopeInterface::SCOPE_STORE, $store);
    }
    
    public function getHost($store = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_HOST, ScopeInterface::SCOPE_STORE, $store);
    }
    
    public function getSiteKey($store = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SITE_KEY, ScopeInterface::SCOPE_STORE, $store);
    }
    
    public function getAddDiv($store = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ADD_DIV, ScopeInterface::SCOPE_STORE, $store);
    }
    
    public function getAddScripts($store = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ADD_SCRIPTS, ScopeInterface::SCOPE_STORE, $store);
    }
    
    public function getHideContent($store = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_HIDE_CONTENT, ScopeInterface::SCOPE_STORE, $store);
    }
    
    public function getNavToSearch($store = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_NAV_TO_SEARCH, ScopeInterface::SCOPE_STORE, $store);
    }
    
    public function getNavToSearchEnableBlacklist($store = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_NAV_TO_SEARCH_ENABLE_BLACKLIST,
            ScopeInterface::SCOPE_STORE, $store);
    }
    
    public function getNavToSearchBlacklist($store = null)
    {
        $value = $this->scopeConfig->getValue(
            self::XML_PATH_NAV_TO_SEARCH_BLACKLIST,
            ScopeInterface::SCOPE_STORE, $store);
        $value = empty($value) ? array() : explode(',', $value);
        return $value;
    }
    
    public function getScriptPath($store = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SCRIPT_PATH, ScopeInterface::SCOPE_STORE, $store);
    }
    
    public function getClientConfigPath($store = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CLIENT_CONFIG_PATH, ScopeInterface::SCOPE_STORE, $store);
    }
    
    public function getClientConfigFilename($store = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CLIENT_CONFIG_FILENAME, ScopeInterface::SCOPE_STORE, $store);
    }
    
    public function getJqueryFilename()
    {
        /* TODO: move to config file */
        return 'jquery.1.7.Celebros.min.js';
    }
    
}