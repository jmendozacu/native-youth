<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_CustomerAttributes
 */
namespace Amasty\CustomerAttributes\Plugin\Quote;

use Magento\Quote\Api\Data\PaymentInterface;
use Amasty\CustomerAttributes\Helper\Session;

class QuoteManagement
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var Session
     */
    protected $_sessionHelper;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        Session $sessionHelper,
        \Magento\Framework\Registry $registry
    )
    {
        $this->_objectManager = $objectManager;
        $this->_sessionHelper = $sessionHelper;
        $this->_registry = $registry;
    }

    /**
     * @param QuoteManagement  $quote
     * @param \Closure         $proceed
     * @param string           $cartId
     * @param PaymentInterface $paymentMethods
     */
    public function aroundPlaceOrder(
         $quote,
         \Closure $proceed,
         $cartId,
         $paymentMethods = null
    ) {
        $customAttributes = $this->_sessionHelper->getCustomerAttributesFromSession();
        $orderId = $proceed($cartId, $paymentMethods);

        if ($customAttributes) {
            $this->_saveCustomerAttributesGuest($orderId, $customAttributes);
        }
        return $orderId;

    }

    protected function _saveCustomerAttributesGuest($orderId, $customAttributes){
        $model = $this->_objectManager->create('Amasty\CustomerAttributes\Model\Customer\GuestAttributes');
        /* var $model \Amasty\CustomerAttributes\Model\Customer\GuestAttributes */
        $model->setData($customAttributes);
        $model->setOrderId($orderId);
        $model->save();
    }
}
