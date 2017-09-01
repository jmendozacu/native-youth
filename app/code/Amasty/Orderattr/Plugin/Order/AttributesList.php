<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Orderattr
 */

namespace Amasty\Orderattr\Plugin\Order;

use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Quote\Model\QuoteManagement;

class AttributesList
{

    public function beforeSetCustomAttributes(
        \Magento\Quote\Model\Quote\Address $subject,
        array $attributes
    )
    {
        $orderAttributes = $this->filterOrderAttributesFromCheckout($attributes);

        $subject->setData('order_attributes', $orderAttributes);
    }

    protected function filterOrderAttributesFromCheckout($orderAttributes)
    {
        $orderAttributesList = [];
        foreach ($orderAttributes as $attributeCode => $attributeValue) {
            if (strpos($attributeCode, 'amorderattr_') !== false) {
                $orderAttributesList[str_replace('amorderattr_', '', $attributeCode)] = $attributeValue;
            }
        }
        return $orderAttributesList;
    }
}
