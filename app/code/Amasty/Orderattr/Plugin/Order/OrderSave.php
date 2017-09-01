<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Orderattr
 */

/**
 * Copyright © 2016 Amasty. All rights reserved.
 */

namespace Amasty\Orderattr\Plugin\Order;


class OrderSave
{
    /**
     * @var \Amasty\Orderattr\Helper\Session
     */
    protected $sessionHelper;

    /**
     * @var \Amasty\Orderattr\Model\OrderAttributesManagement
     */
    protected $orderAttributesManagement;

    /**
     * OrderSave constructor.
     *
     * @param \Amasty\Orderattr\Helper\Session                  $sessionHelper
     * @param \Amasty\Orderattr\Model\OrderAttributesManagement $orderAttributesManagement
     */
    public function __construct(
        \Amasty\Orderattr\Helper\Session $sessionHelper,
        \Amasty\Orderattr\Model\OrderAttributesManagement $orderAttributesManagement
    ) {
        $this->sessionHelper = $sessionHelper;
        $this->orderAttributesManagement = $orderAttributesManagement;
    }

    /**
     * @param \Magento\Sales\Api\OrderRepositoryInterface $subject
     * @param \Magento\Sales\Api\Data\OrderInterface      $order
     *
     * @return \Magento\Sales\Api\Data\OrderInterface
     */
    public function afterSave(\Magento\Sales\Api\OrderRepositoryInterface $subject, \Magento\Sales\Api\Data\OrderInterface $order)
    {
        $orderAttributesRow = $this->sessionHelper->getPreparedOrderAttributesFromSession($order->getQuoteId());
        if(!empty($orderAttributesRow)) {
            $this->orderAttributesManagement->saveOrderAttributes(
                $order->getEntityId(), $orderAttributesRow, 'is_visible_on_front'
            );
        }
        return $order;
    }
}
