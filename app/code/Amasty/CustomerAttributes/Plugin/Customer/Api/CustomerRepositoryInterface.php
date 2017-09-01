<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_CustomerAttributes
 */

namespace Amasty\CustomerAttributes\Plugin\Customer\Api;

class CustomerRepositoryInterface
{
    /**
     * @var \Magento\Customer\Model\AttributeMetadataDataProvider
     */
    protected $_attributeMetadataDataProvider;
    /**
     * @var \Magento\Framework\Validator\UniversalFactory
     */
    protected $_universalFactory;
    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $_eavConfig;
    /**
     * @var \Magento\Eav\Model\Entity\Attribute\OptionFactory
     */
    private $optionFactory;

    public function __construct(
        \Magento\Customer\Model\AttributeMetadataDataProvider $attributeMetadataDataProvider,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Eav\Model\Entity\Attribute\OptionFactory $optionFactory,
        \Magento\Framework\Validator\UniversalFactory $universalFactory
    ) {
        $this->_attributeMetadataDataProvider = $attributeMetadataDataProvider;
        $this->_universalFactory = $universalFactory;
        $this->_eavConfig = $eavConfig;
        $this->optionFactory = $optionFactory;
    }

    public function beforeSave($subject, $customer, $passwordHash = null){
        if ($customer->getCustomAttributes()) {
            $entityType = $this->_eavConfig->getEntityType('customer');
            $attributes = $this->_universalFactory->create(
                $entityType->getEntityAttributeCollection()
            )->setEntityTypeFilter(
                $entityType
            )->addFieldToFilter('type_internal', 'selectgroup')
                ->getData();

            foreach ($attributes as $attribute) {
                $data = $customer->getCustomAttribute($attribute['attribute_code']);
                if ($data && $data->getValue()) {
                    $option = $this->optionFactory->create()->load($data->getValue());
                    $gr = $option->getGroupId();
                    if($gr) {
                        $customer->setGroupId($option->getGroupId());
                    }
                }
            }
        }

        return [$customer, $passwordHash];
    }
}
