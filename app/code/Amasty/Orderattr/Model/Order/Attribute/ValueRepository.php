<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Orderattr
 */


namespace Amasty\Orderattr\Model\Order\Attribute;

use Amasty\Orderattr\Model\Order\Attribute\Value;

class ValueRepository implements \Amasty\Orderattr\Api\OrderAttributeValueRepositoryInterface
{
    /**
     * @var Value[]
     */
    private $attributes = [];

    /**
     * @var ValueFactory
     */
    private $valueFactory;
    /**
     * @var \Amasty\Orderattr\Model\ResourceModel\Order\Attribute\CollectionFactory
     */
    private $attributeCollectionFactory;

    public function __construct(
        \Amasty\Orderattr\Model\Order\Attribute\ValueFactory $valueFactory,
        \Amasty\Orderattr\Model\ResourceModel\Order\Attribute\CollectionFactory $attributeCollectionFactory
    ) {
        $this->valueFactory = $valueFactory;
        $this->attributeCollectionFactory = $attributeCollectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getByOrder($orderId)
    {
        if (!isset($this->attributes[$orderId])) {
            $attribute = $this->valueFactory->create()->loadByOrderId($orderId);
            if (!$attribute->getOrderEntityId()) {
                $attribute->setOrderEntityId($orderId);
            }
            $this->attributes[$orderId] = $attribute;
        }

        return $this->attributes[$orderId];
    }

    /**
     * {@inheritdoc}
     */
    public function save(\Amasty\Orderattr\Api\Data\OrderAttributeValueInterface $orderAttribute)
    {
        if (!$orderAttribute->getOrderEntityId()) {
            return false;
        }
        $orderAttribute = $this->getByOrder($orderAttribute->getOrderEntityId())->addData($orderAttribute->getData());

        $collection = $this->attributeCollectionFactory->create()->addFieldToFilter('include_api', 1);
        foreach ($collection as $attributeConfig) {
            foreach ($orderAttribute->getAttributes() as $attributeValue) {
                if ($attributeConfig->getAttributeCode() != $attributeValue->getAttributeCode()) {
                    continue;
                }
                $value = is_array($attributeValue->getValue()) ? implode(',', $attributeValue->getValue())
                    : $attributeValue->getValue();
                $orderAttribute->setData($attributeConfig->getAttributeCode(), $value);

                $orderAttribute->setData(
                    $attributeConfig->getAttributeCode() . '_output',
                    $orderAttribute->prepareAttributeValue($attributeConfig)
                );
                break;
            }
        }

        $orderAttribute->save();

        return $orderAttribute;
    }
}
