<?php
namespace VS\Minilogin\Block\Customer\Account;

use Magento\Framework\View\Element\Template;

class Form extends Template
{

    /**
     * Customer session
     *
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * @var \Magento\Customer\Model\Url
     */
    protected $_customerUrl;


    /**
     * @param Template\Context $context
     * @param \Magento\Customer\Model\Url $customerUrl
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Customer\Model\Url $customerUrl,
        \Magento\Customer\CustomerData\JsLayoutDataProviderPoolInterface $jsLayoutDataProvider,
        array $data = [])
    {
        $this->httpContext = $httpContext;
        if (isset($data['jsLayout'])) {
            $this->jsLayout = array_merge_recursive($jsLayoutDataProvider->getData(), $data['jsLayout']);
            unset($data['jsLayout']);
        } else {
            $this->jsLayout = $jsLayoutDataProvider->getData();
        }

        parent::__construct($context,$data);
        $this->_customerUrl = $customerUrl;
    }
    /**
     * @return string
     */
    public function getHref()
    {
        return $this->isLoggedIn()
            ? $this->_customerUrl->getLogoutUrl()
            : $this->_customerUrl->getLoginUrl();
    }

    /**
     * @return string
     */
    public function getHrefTitle()
    {
        return $this->isLoggedIn()
            ? 'Logout'
            : 'Login';
    }

    public function getForgotPasswordActionUrl(){

        return $this->getUrl('customer/account/forgotpasswordpost');
    }

    /**
     * Returns popup config
     *
     * @return array
     */
    public function getConfig()
    {
        return [
            'customerRegisterUrl' => $this->getCustomerRegisterUrlUrl(),
            'customerForgotPasswordUrl' => $this->getCustomerForgotPasswordUrl(),
            'baseUrl' => $this->getBaseUrl()
        ];
    }

    /**
     * Return base url.
     *
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl();
    }

    /**
     * Get customer register url
     *
     * @return string
     */
    public function getCustomerRegisterUrlUrl()
    {
        return $this->getUrl('customer/account/create');
    }

    /**
     * Get customer forgot password url
     *
     * @return string
     */
    public function getCustomerForgotPasswordUrl()
    {
        return $this->getUrl('customer/account/forgotpassword');
    }

    /**
     * Is logged in
     *
     * @return bool
     */
    public function isLoggedIn()
    {
        return $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
    }
}