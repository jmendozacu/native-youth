<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Orderattr
 */

namespace Amasty\Orderattr\Ui\Component\Listing;

/**
 * Class Columns
 */
class Columns extends \Magento\Ui\Component\Listing\Columns
{
    /**
     * Default columns max order
     */
    const DEFAULT_COLUMNS_MAX_ORDER = 100;

    /** @var \Amasty\Orderattr\Ui\Component\Listing\Attribute\RepositoryInterface */
    protected $attributeRepository;

    /**
     * @var array
     */
    protected $filterMap
        = [
            'default'     => 'text',
            'select'      => 'select',
            'boolean'     => 'select',
            'multiselect' => 'select',
            'radios'      => 'select',
            'checkboxes'  => 'select',
            'date'        => 'dateRange',
            'datetime'    => 'dateRange',
        ];

    /**
     * @var \Amasty\Orderattr\Ui\Component\ColumnFactory
     */
    protected $columnFactory;

    /**
     * @var \Amasty\Orderattr\Helper\Config
     */
    protected $config;

    /**
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
     * @param \Amasty\Orderattr\Ui\Component\ColumnFactory $columnFactory
     * @param \Amasty\Orderattr\Ui\Component\Listing\Attribute\RepositoryInterface $attributeRepository
     * @param array $components
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Amasty\Orderattr\Ui\Component\ColumnFactory $columnFactory,
        \Amasty\Orderattr\Ui\Component\Listing\Attribute\RepositoryInterface $attributeRepository,
        \Amasty\Orderattr\Helper\Config $config,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $components, $data);
        $this->columnFactory = $columnFactory;
        $this->attributeRepository = $attributeRepository;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function prepare()
    {
        if ($this->allowToAddAttributes()) {
            $this->prepareOrderAttributes();
        }
        parent::prepare();
    }

    protected function prepareOrderAttributes()
    {
        $columnSortOrder = self::DEFAULT_COLUMNS_MAX_ORDER;
        foreach ($this->attributeRepository->getList() as $attribute) {
            $config = [];
            if (!isset($this->components[$attribute->getAttributeCode()])) {
                $config['sortOrder'] = ++$columnSortOrder;
                $config['add_field'] = false;
                $config['visible'] = true;
                $config['filter'] = $this->getFilterType($attribute->getFrontendInput());
                $column = $this->columnFactory->create($attribute, $this->getContext(), $config);
                $column->prepare();
                $this->addComponent($attribute->getAttributeCode(), $column);
            }
        }
    }

    /**
     * Retrieve filter type by $frontendInput
     *
     * @param string $frontendInput
     * @return string
     */
    protected function getFilterType($frontendInput)
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
        $orderAttributesList = $this->attributeRepository->getList();
        foreach ($orderAttributesList as $attribute) {
            /**
             * @var \Magento\Eav\Model\Entity\Attribute $attribute
             */
            if ($attribute->getFrontendInput() == 'checkboxes') {
                $dataSource = $this->prepareDataForCheckboxes(
                    $dataSource, $attribute->getAttributeCode()
                );
            }
        }
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
