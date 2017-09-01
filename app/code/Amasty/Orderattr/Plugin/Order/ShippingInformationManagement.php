<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Orderattr
 */

namespace Amasty\Orderattr\Plugin\Order;

use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Quote\Model\QuoteManagement;

class ShippingInformationManagement
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

    public function aroundSaveAddressInformation(
        \Magento\Checkout\Model\ShippingInformationManagement $subject,
        \Closure $proceed,
        $cartId,
        \Magento\Checkout\Model\ShippingInformation $addressInformation
    )
    {
        $orderAttributes = $addressInformation->getShippingAddress()->getOrderAttributes();
        $this->sessionHelper->setOrderAttributesToSession($cartId, $orderAttributes);
        return $proceed($cartId, $addressInformation);
    }
}
