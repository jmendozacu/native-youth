<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_CustomerAttributes
 */

namespace Amasty\CustomerAttributes\Block\Adminhtml\Customer\Attribute\Edit\Tab;

class Reports extends \Magento\Backend\Block\Widget
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;
    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory
     */
    protected $collectionFactory;
    /**
     * @var \Amasty\CustomerAttributes\Model\ResourceModel\Customer\GuestAttributes\CollectionFactory
     */
    protected $collectionGuestFactory;
    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory
     */
    protected $collectionOptionFactory;

    /**
     * Reports constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $collectionFactory
     * @param \Amasty\CustomerAttributes\Model\ResourceModel\Customer\GuestAttributes\CollectionFactory $collectionGuestFactory
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $collectionOptionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $collectionFactory,
        \Amasty\CustomerAttributes\Model\ResourceModel\Customer\GuestAttributes\CollectionFactory $collectionGuestFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $collectionOptionFactory,
        array $data
    ) {
        $this->registry = $registry;
        $this->collectionFactory = $collectionFactory;
        $this->collectionGuestFactory = $collectionGuestFactory;
        $this->collectionOptionFactory = $collectionOptionFactory;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('reports.phtml');
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getAttributeValues()
    {
        $model  = $this->registry->registry('entity_attribute');
        $customerCollection = $this->collectionFactory->create();

        $customerCollection->addAttributeToSelect($model->getAttributeCode());
        $customerCollection->addAttributeToFilter(
            $model->getAttributeCode(), array('notnull' => true)
        );
        $customerValue = $customerCollection->getColumnValues(
            $model->getAttributeCode()
        );

        $guestCollection = $this->collectionGuestFactory->create();
        $guestCollection->addFieldToFilter(
            $model->getAttributeCode(), array('notnull' => true)
        );
        $guestValue = $guestCollection->getColumnValues(
            $model->getAttributeCode()
        );

        $valuesAsString = array_merge($customerValue, $guestValue);

        $valuesAsArray = array();
        foreach ($valuesAsString as $attrIdsString) {
            $valuesAsArray = array_merge(
                $valuesAsArray, explode(',', $attrIdsString)
            );
        }

        $optionCollection = $this->collectionOptionFactory->create();
        $optionCollection->setAttributeFilter($model->getAttributeId())
            ->addFieldToFilter(
                'main_table.option_id', array('in' => $valuesAsArray)
            )
            ->setStoreFilter();

        $qtyValues = array_count_values($valuesAsArray);

        $result = array();
        if (!$qtyValues) {
            return $result;
        }

        $options = $optionCollection->toOptionArray();
        if($model->getFrontendInput() == 'boolean') {
            $options = [
                ['label' => __('No'),  'value' => '0'],
                ['label' => __('Yes'), 'value' => '1']
            ];
        }
        $sum = array_sum($qtyValues);
        foreach ($options as $value) {
            if (array_key_exists($value['value'], $qtyValues)) {
                $qty = $qtyValues[$value['value']];
                $label = $value['label'] . ' - ' . $qty . ' (' . round(
                        ($qty / $sum) * 100, 1
                    ) . '%)';
                $result[$value['value']] = [
                    'qty' => $qty,
                    'label' => $label
                ];
            }
        }

        return $result;
    }
}
