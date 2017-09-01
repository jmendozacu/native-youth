<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_CustomerAttributes
 */

namespace Amasty\CustomerAttributes\Ui\Component\Listing;

/**
 * Class Columns
 */
class Columns extends \Magento\Ui\Component\Listing\Columns
{
    /**
     * Default columns max order
     */
    const DEFAULT_COLUMNS_MAX_ORDER = 100;

    /**
     * @var array
     */
    protected $filterMap
        = [
            'default'       => 'text',
            'select'        => 'select',
            'boolean'       => 'select',
            'multiselect'   => 'select',
            'multiselectimg'=> 'select',
            'selectimg'     => 'select',
            'selectgroup'   => 'select',
            'date'          => 'dateRange',
            'datetime'      => 'dateRange',
        ];

    /**
     * @var \Amasty\CustomerAttributes\Ui\Component\ColumnFactory
     */
    protected $columnFactory;

    /**
     * @var \Magento\Customer\Model\AttributeMetadataDataProvider
     */
    protected $attributeMetadataDataProvider;

    /**
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
     * @param \Amasty\CustomerAttributes\Ui\Component\ColumnFactory $columnFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Customer\Model\AttributeMetadataDataProvider $attributeMetadataDataProvider,
        \Amasty\CustomerAttributes\Ui\Component\ColumnFactory $columnFactory,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $components, $data);
        $this->columnFactory = $columnFactory;
        $this->attributeMetadataDataProvider = $attributeMetadataDataProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function prepare()
    {
        if ($this->allowToAddAttributes()) {
            $this->prepareAttributes();
        }
        parent::prepare();
    }

    protected function prepareAttributes()
    {
        $columnSortOrder = self::DEFAULT_COLUMNS_MAX_ORDER;
        $attributes = $this->attributeMetadataDataProvider->loadAttributesCollection(
            'customer',
            'amasty_custom_attribute'
        );

        foreach ($attributes as $attribute) {
            /* @var $attribute \Magento\Eav\Model\Entity\Attribute */
            if($attribute->getUsedInOrderGrid()) {
                $config = [];
                if (!isset($this->components[$attribute->getAttributeCode()])) {
                    $config['sortOrder'] = ++$columnSortOrder;
                    $config['add_field'] = false;
                    $config['visible'] = true;
                    $config['default'] = false;
                    $config['filter'] = $this->getCustomerAttributeFilterType($attribute->getFrontendInput());
                    $column = $this->columnFactory->create($attribute, $this->getContext(), $config);
                    $column->prepare();
                    $this->addComponent($attribute->getAttributeCode(), $column);
                }
            }
        }
    }

    /**
     * Retrieve filter type by $frontendInput
     *
     * @param string $frontendInput
     * @return string
     */
    protected function getCustomerAttributeFilterType($frontendInput)
    {
        return isset($this->filterMap[$frontendInput]) ? $this->filterMap[$frontendInput] : $this->filterMap['default'];
    }

    public function prepareDataSource(array $dataSource)
    {

        if ($this->allowToAddAttributes()) {
            $dataSource = $this->prepareDataForOrderAttributes($dataSource);
        }
        return parent::prepareDataSource($dataSource);
    }

    protected function prepareDataForOrderAttributes(array $dataSource)
    {
        /*$orderAttributesList = $this->attributeRepository->getList();
        foreach ($orderAttributesList as $attribute) {
            /**
             * @var \Magento\Eav\Model\Entity\Attribute $attribute
             */
       /*     if ($attribute->getFrontendInput() == 'checkboxes') {
                $dataSource = $this->prepareDataForCheckboxes(
                    $dataSource, $attribute->getAttributeCode()
                );
            }
        }*/
        return $dataSource;
    }

    protected function prepareDataForCheckboxes(array $dataSource, $attributeCode)
    {
        $items = &$dataSource['data']['items'];
        foreach ($items as &$item) {
            if (array_key_exists($attributeCode, $item)) {
                $item[$attributeCode] = explode(',', $item[$attributeCode]);
            }
        }

        return $dataSource;
    }

    public function allowToAddAttributes()
    {
        return true;
    }

}
