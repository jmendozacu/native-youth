<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Orderattr
 */

namespace Amasty\Orderattr\Plugin\Order;

use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Quote\Model\QuoteManagement;

class PaymentInformationManagement
{

    /**
     * @var \Amasty\Orderattr\Helper\Session
     */
    protected $sessionHelper;

    public function __construct(
        \Amasty\Orderattr\Helper\Session $sessionHelper
    )
    {
        $this->sessionHelper = $sessionHelper;
    }

    public function aroundSavePaymentInformation(
        \Magento\Checkout\Model\PaymentInformationManagement $subject,
        \Closure $proceed,
        $cartId,
        \Magento\Quote\Api\Data\PaymentInterface $paymentMethod,
        \Magento\Quote\Api\Data\AddressInterface $billingAddress = null
    )
    {
        $orderAttributes = $billingAddress->getOrderAttributes();
        $this->sessionHelper->setOrderAttributesToSession($cartId, $orderAttributes);
        return $proceed($cartId, $paymentMethod, $billingAddress);
    }
}
