<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Orderattr
 */


namespace Amasty\Orderattr\Api\Data;

/**
 * @api
 */
interface OrderAttributeValueInterface
{
    const ORDER_ENTITY_ID = 'order_entity_id';
    const CUSTOMER_ID = 'customer_id';
    const CREATED_AT = 'created_at';

    /**
     * @return int
     */
    public function getOrderEntityId();

    /**
     * @param int $orderId
     *
     * @return $this
     */
    public function setOrderEntityId($orderId);

    /**
     * @return int
     */
    public function getCustomerId();

    /**
     * @param int $customerId
     *
     * @return $this
     */
    public function setCustomerId($customerId);

    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @param string $date
     *
     * @return $this
     */
    public function setCreatedAt($date);

    /**
     * @return \Amasty\Orderattr\Api\Data\OrderAttributeDataInterface[]
     */
    public function getAttributes();

    /**
     * @param \Amasty\Orderattr\Api\Data\OrderAttributeDataInterface[] $attributes
     *
     * @return $this
     */
    public function setAttributes($attributes);
}
