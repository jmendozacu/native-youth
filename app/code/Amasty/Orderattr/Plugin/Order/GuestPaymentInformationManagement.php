<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Orderattr
 */

namespace Amasty\Orderattr\Plugin\Order;

use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Quote\Model\QuoteManagement;

class GuestPaymentInformationManagement
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
        \Magento\Checkout\Model\GuestPaymentInformationManagement $subject,
        \Closure $proceed,
        $cartId,
        $email,
        \Magento\Quote\Api\Data\PaymentInterface $paymentMethod,
        \Magento\Quote\Api\Data\AddressInterface $billingAddress = null
    ) {
        if($billingAddress) {
            $orderAttributes = $billingAddress->getOrderAttributes();
            $this->sessionHelper->setOrderAttributesToSession($cartId, $orderAttributes);
        }

        return $proceed($cartId,  $email, $paymentMethod, $billingAddress);
    }
}
