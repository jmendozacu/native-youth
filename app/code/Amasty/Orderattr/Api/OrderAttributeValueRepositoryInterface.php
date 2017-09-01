<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Orderattr
 */


namespace Amasty\Orderattr\Api;

/**
 * @api
 */
interface OrderAttributeValueRepositoryInterface
{
    /**
     * Loads a specified order.
     *
     * @param int $id The order ID.
     * @return \Magento\Sales\Api\Data\OrderInterface Order interface.
     */
    public function getByOrder($orderId);

    /**
     * Performs persist operations for a specified order.
     *
     * @param \Amasty\Orderattr\Api\Data\OrderAttributeValueInterface  $entity
     * @return \Amasty\Orderattr\Api\Data\OrderAttributeValueInterface|bool
     */
    public function save(\Amasty\Orderattr\Api\Data\OrderAttributeValueInterface $entity);
}
