<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_CustomerAttributes
 */

namespace Amasty\CustomerAttributes\Plugin\Customer\Model;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Amasty\CustomerAttributes\Component\Form\AttributeMapper;

class DataProvider
{
    protected $objectManager;
    /**
     * @var \Magento\Customer\Model\AttributeMetadataDataProvider
     */
    protected $attributeMetadataDataProvider;
    /**
     * @var AttributeMapper
     */
    protected $attributeMapper;

    /**
     * @var \Magento\Framework\Filesystem
     */


    public function __construct(
        \Magento\Customer\Model\AttributeMetadataDataProvider $attributeMetadataDataProvider,
        AttributeMapper $attributeMapper,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
        $this->attributeMetadataDataProvider = $attributeMetadataDataProvider;
        $this->attributeMapper = $attributeMapper;
    }

    /**
     * set magento data model for checkxoxes and radios
     * @param $subject
     * @param $result
     * @return mixed
     */
    public function afterGetFieldsMetaInfo($subject, $result){
        /** @var \Magento\Eav\Api\Data\AttributeInterface[] $attributes */
        $attributes = $this->attributeMetadataDataProvider->loadAttributesCollection(
            'customer', 'amasty_custom_attribute'
        );
        $customerAttributes = $codes =  [];
        foreach ($attributes as $attribute) {
            $customerAttributes[$attribute->getAttributeCode()] = $attribute;
            $codes[] = $attribute->getAttributeCode();
        }

        foreach ($result as $name => $meta ) {
            if (in_array($name, $codes)) {
                $result[$name] = $this->attributeMapper->map($customerAttributes[$name]);
            }
        }

        return $result;
    }
}
