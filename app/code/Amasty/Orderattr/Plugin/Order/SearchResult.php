<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Orderattr
 */

namespace Amasty\Orderattr\Plugin\Order;


class SearchResult
{
    /**
     * @var \Amasty\Orderattr\Helper\Config
     */
    protected $config;
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;

    protected $columns;

    public function __construct(
        \Amasty\Orderattr\Helper\Config $config,
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->config = $config;
        $this->resource = $resource;
    }

    public function afterGetSelect(
        \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult $collection,
        $select
    ) {
        if ((string)$select == "") {
            return $select;
        }

        $attributeFieldTableName = $collection->getTable(
            'amasty_orderattr_order_attribute_value'
        );
        if (!$this->columns) {
            $connection = $this->resource->getConnection();
            $fields = $connection->describeTable($attributeFieldTableName);
            unset($fields['created_at']);
            $tmp = [];
            foreach ($fields as $field => $value) {
                $tmp[] = 'amorderattr.' . $field;
            }

            $this->columns = $tmp;
        }

        if ($collection->getResource() instanceof \Magento\Sales\Model\ResourceModel\Order ) {
            if (!array_key_exists('amorderattr', $select->getPart('from'))) {
                $select->joinLeft(['amorderattr' => $attributeFieldTableName],
                    'main_table.entity_id = amorderattr.order_entity_id',
                    $this->columns
                );
            }
        }

        if ($collection->getResource() instanceof \Magento\Sales\Model\ResourceModel\Order\Invoice) {
            if ($this->config->getShowInvoiceGrid()
                && !array_key_exists('amorderattr', $select->getPart('from'))) {
                $select->joinLeft(
                    ['amorderattr' => $attributeFieldTableName],
                    'main_table.order_id = amorderattr.order_entity_id',
                    $this->columns
                );
            }
        }

        if ($collection->getResource() instanceof \Magento\Sales\Model\ResourceModel\Order\Shipment) {
            if ($this->config->getShowShipmentGrid()
                && !array_key_exists('amorderattr', $select->getPart('from'))) {
                $select->joinLeft(
                    ['amorderattr' => $attributeFieldTableName],
                    'main_table.order_id = amorderattr.order_entity_id',
                    $this->columns
                );
            }
        }

        return $select;
    }
}
