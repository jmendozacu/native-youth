<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Orderattr
 */

namespace Amasty\Orderattr\Helper;


use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Session\Storage;
use Magento\Checkout\Model\Session as CheckoutSession;

class Session extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @var Storage
     */
    protected $session;

    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    public function __construct(
        Context $context,
        Storage $sessionStorage,
        CheckoutSession $checkoutSession,
        CustomerSession $customerSession
    )
    {
        $this->session = $sessionStorage;
        $this->checkoutSession = $checkoutSession;
        $this->customerSession = $customerSession;
        parent::__construct($context);
    }

    public function setOrderAttributesToSession($cartId, $orderAttributes)
    {
        $existsOrderAttributes = $this->getOrderAttributesFromSession($cartId);
        $orderAttributes = $orderAttributes ?: [];
        $orderAttributes = $existsOrderAttributes
            ? array_merge($existsOrderAttributes, $orderAttributes)
            : $orderAttributes;

        $orderAttributes = $orderAttributes ?: [];
        $this->session->setData(
            $this->getOrderAttributesSessionKey($cartId), $orderAttributes
        );
    }

    public function getPreparedOrderAttributesFromSession($cartId)
    {
        $orderAttributesRow = $this->getOrderAttributesFromSession($cartId);
        if(!$orderAttributesRow) {
            return [];
        }

        $orderAttributes = [];
        foreach ($orderAttributesRow as $orderAttributeCode => $orderAttribute) {
            /**
             * @var \Magento\Framework\Api\AttributeValue $orderAttribute
             */
            $orderAttributes[$orderAttributeCode] = $orderAttribute->getValue();
        }
        if ($customerId = $this->getCustomerId()) {
            $orderAttributes['customer_id'] = $customerId;
        }
        return $orderAttributes;
    }

    public function getOrderAttributesFromSession($cartId)
    {
        return $this->session->getData($this->getOrderAttributesSessionKey($cartId));
    }

    public function getOrderAttributesSessionKey($cartId)
    {
        return 'amasty_order_attributes_quote_';//.$cartId;
    }

    public function getLastQuoteId()
    {
        return $this->checkoutSession->getLastSuccessQuoteId();
    }

    public function getCustomerId()
    {
        return $this->customerSession->getCustomerId();
    }
}
