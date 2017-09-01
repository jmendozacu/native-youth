<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Orderattr
 */

namespace Amasty\Orderattr\Plugin\Ui\Model;


class Manager
{

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Amasty\Orderattr\Model\AttributeMetadataDataProvider
     */
    protected $attributeMetadataDataProvider;

    public function __construct(
        \Amasty\Orderattr\Model\AttributeMetadataDataProvider $attributeMetadataDataProvider,
        \Magento\Framework\Registry $registry
    )
    {
        $this->registry = $registry;
        $this->attributeMetadataDataProvider = $attributeMetadataDataProvider;
    }

    const UI_GRID_ID      = 'sales_order_grid';
    const UI_GRID_COLUMNS = 'sales_order_columns';

    /**
     * @param \Magento\Ui\Model\Manager $subject
     * @param                           $result
     *
     * @return mixed
     */
    public function afterGetData(
        \Magento\Ui\Model\Manager $subject,
        $result
    ) {
        if (!$this->isOrderGrid($result) || $this->registry->registry('amorderattr_order_grid')) {
            return $result;
        }
        $fields = &$result[static::UI_GRID_ID]['children'][static::UI_GRID_COLUMNS]['children'];
        
        $orderAttributesFields = $this->generateOrderColumns();
        $fields = array_merge($fields, $orderAttributesFields);

        $this->registry->register('amorderattr_order_grid', 1);
        return $result;
    }

    protected function isOrderGrid($result)
    {
        return array_key_exists(static::UI_GRID_ID, $result);
    }

    protected function generateOrderColumns()
    {
        $orderAttributes = $this->attributeMetadataDataProvider->loadAttributesForOrderGrid();

        $orderAttributeColumns = [];

        foreach ($orderAttributes as $orderAttribute) {
            /**
             * @var \Amasty\Orderattr\Model\ResourceModel\Eav\Attribute $orderAttribute
             */
            $orderAttributeColumns[$orderAttribute->getAttributeCode()]
                = $this->generateOrderAttributeColumn($orderAttribute);
        }

        return $orderAttributeColumns;
    }

    /**
     * @param \Amasty\Orderattr\Model\ResourceModel\Eav\Attribute $orderAttribute
     *
     * @return array
     */
    protected function generateOrderAttributeColumn($orderAttribute)
    {
        $result = [
            'children'   => [],
            'arguments'  => [
                'data' => [
                    'name'     => 'data',
                    'xsi:type' => 'array',
                    'item'     => [
                        'state_prefix' => [
                            'name'     => 'state_prefix',
                            'xsi:type' => 'string',
                            'value'    => 'columns',
                        ],
                        'config'       => [
                            'name'     => 'config',
                            'xsi:type' => 'array',
                            'item'     => [
                                'component' => [
                                    'name'     => 'component',
                                    'xsi:type' => 'string',
                                    'value'    => 'Magento_Ui/js/grid/columns/column',
                                ],
                                'dataType'  => [
                                    'name'     => 'dataType',
                                    'xsi:type' => 'string',
                                    'value'    => 'text',
                                ],
                                'visible' => [
                                    'name' => 'visible',
                                    'xsi:type' => 'boolean',
                                    'value' => 'true',
                                ],
                                'sortable' => [
                                    'name' => 'visible',
                                    'xsi:type' => 'boolean',
                                    'value' => 'false',
                                ],
//                                'filter'    => [
//                                    'name'     => 'filter',
//                                    'xsi:type' => 'string',
//                                    'value'    => 'text',
//                                ],
//                                'sorting'   => [
//                                    'name'     => 'sorting',
//                                    'xsi:type' => 'string',
//                                    'value'    => 'desc',
//                                ],
                                'label'     => [
                                    'name'      => 'label',
                                    'xsi:type'  => 'string',
                                    'translate' => 'true',
                                    'value'     => $orderAttribute->getFrontendLabel(),
                                ],
                            ]
                        ]
                    ],
                ]
            ],
            'attributes' => [
                'class' => 'Magento\Ui\Component\Listing\Columns\Column',
                'name'  => $orderAttribute->getAttributeCode()
            ]
        ];

        return $result;
    }

}
