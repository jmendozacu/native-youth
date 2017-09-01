<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Orderattr
 */

namespace Amasty\Orderattr\Model;

use Amasty\Orderattr\Helper\Session;

/**
 * Attribute Metadata data provider class
 */
class OrderAttributesManagement
{

    /**
     * @var Session
     */
    protected $sessionHelper;

    /**
     * @var \Amasty\Orderattr\Model\Order\Attribute\ValueFactory
     */
    protected $orderAttributesValueFactory;

    /**
     * @var AttributeMetadataDataProvider
     */
    protected $attributeMetadataDataProvider;

    /** @var ResourceModel\Order\Attribute\CollectionFactory  */
    protected $orderAttributeCollectionFactory;

    public function __construct(
        Session $sessionHelper,
        \Amasty\Orderattr\Model\Order\Attribute\ValueFactory $orderAttributeValueFactory,
        \Amasty\Orderattr\Model\AttributeMetadataDataProvider $attributeMetadataDataProvider,
        \Amasty\Orderattr\Model\ResourceModel\Order\Attribute\CollectionFactory $orderAttributeCollectionFactory
    ){
        $this->sessionHelper = $sessionHelper;
        $this->orderAttributesValueFactory = $orderAttributeValueFactory;
        $this->attributeMetadataDataProvider = $attributeMetadataDataProvider;
        $this->orderAttributeCollectionFactory = $orderAttributeCollectionFactory;
    }

    /**
     * @inheritdoc
     */
    public function saveOrderAttributesToSession(
        $cartId, $orderAttributes
    ) {

        $this->sessionHelper->setOrderAttributesToSession(
            $cartId, $orderAttributes
        );
    }

    /**
     * @param int $orderId
     * @param array $orderAttributesData
     */
    public function saveOrderAttributes($orderId, $orderAttributesData, $type)
    {
        $attributes = [];
        $attributesCollection = $this->orderAttributeCollectionFactory->create()
            ->addFieldToFilter('attribute_code',
            ['in' => array_keys($orderAttributesData)]
        );

        foreach($attributesCollection as $attribute){
            $attributes[$attribute->getAttributeCode()] = $attribute;
        }

        $orderAttributes = $this->loadOrderAttributeValuesAndSetOrderId($orderId);

        $defaultOrderAttributeValues = $this->getDefaultValues($type);
        $valuesToInsert = array_merge($defaultOrderAttributeValues, $orderAttributesData);
        foreach ($valuesToInsert as $key => $value) {
            $value = is_array($value) ? implode(',',$value) : $value;
            $orderAttributes->setData($key, $value);

            if (array_key_exists($key, $attributes)){
                $attribute = $attributes[$key];
                $orderAttributes->setData(
                    $key.'_output',
                    $orderAttributes->prepareAttributeValue($attribute)
                );
            }
        }

        $orderAttributes->save();
    }

    protected function getDefaultValues($visibility)
    {
        $defaultValues = [];
        $orderAttributesWithDefaultValues = $this->attributeMetadataDataProvider
            ->loadAttributesWithDefaultValueCollection($visibility);

        foreach ($orderAttributesWithDefaultValues as $orderAttribute) {
            /**
             * @var \Amasty\Orderattr\Model\ResourceModel\Eav\Attribute $orderAttribute
             */
            $defaultValues[$orderAttribute->getAttributeCode()] = $orderAttribute->getDefaultValue();
        }

        return $defaultValues;
    }

    /**
     * @param \Amasty\Orderattr\Model\ResourceModel\Eav\Attribute $orderAttribute
     */
    protected function getDefaultValueFromOrderAttribute($orderAttribute)
    {
       $value = $orderAttribute->getDefaultValue();
    }

    /**
     * @param int $orderId
     *
     * @return \Amasty\Orderattr\Model\Order\Attribute\Value
     */
    protected function loadOrderAttributeValuesAndSetOrderId($orderId)
    {
        /**
         * @var \Amasty\Orderattr\Model\Order\Attribute\Value $orderAttributes
         */
        $orderAttributes = $this->orderAttributesValueFactory->create();
        $orderAttributes->load($orderId, 'order_entity_id');
        if (!$orderAttributes->getOrderEntityId()) {
            $orderAttributes->setOrderEntityId($orderId);
        }

        return $orderAttributes;
    }
}
