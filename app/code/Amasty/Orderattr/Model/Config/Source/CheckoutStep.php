<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Orderattr
 */

namespace Amasty\Orderattr\Model\Config\Source;

class CheckoutStep implements \Magento\Framework\Option\ArrayInterface
{

    public function toOptionArray()
    {
        $array = $this->toArray();
        $optionArray = [];
        foreach ($array as $stepId => $label) {
            $option = ['value' => $stepId, 'label' => $label];
            $optionArray[] = $option;
        }
        return $optionArray;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [
            2 => __('Shipping'),
            3 => __('Review & Payments'),
        ];
    }
}
