<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Orderattr
 */

namespace Amasty\Orderattr\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /** @var \Amasty\Orderattr\Model\ResourceModel\Order\Attribute\CollectionFactory  */
    protected $orderAttributeCollectionFactory;

    /**
     * @param \Amasty\Orderattr\Model\ResourceModel\Order\Attribute\CollectionFactory $orderAttributeCollectionFactory
     */
    public function __construct(
        \Amasty\Orderattr\Model\ResourceModel\Order\Attribute\CollectionFactory
        $orderAttributeCollectionFactory
    ) {
        $this->orderAttributeCollectionFactory = $orderAttributeCollectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.1.0', '<')) {
            $this->addOutputColumns($setup);
        }

        if (version_compare($context->getVersion(), '1.1.1', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('amasty_orderattr_order_eav_attribute'),
                'validate_length_count',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'default' => 25],
                'Validation Length'
            );
        }

        if (version_compare($context->getVersion(), '1.1.6', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('amasty_orderattr_order_eav_attribute'),
                'include_api',
                [
                    'length'   => 1,
                    'nullable' => false,
                    'unsigned' => true,
                    'comment'  => 'Include to API',
                    'default'  => 0,
                    'type'     => Table::TYPE_SMALLINT
                ]
            );
        }

        $setup->endSetup();
    }

    protected function addOutputColumns(SchemaSetupInterface $setup)
    {
        /** @var \Amasty\Orderattr\Model\ResourceModel\Order\Attribute\Collection $collection */
        $collection = $this->orderAttributeCollectionFactory->create();
        $attributesData = $collection->getData();

        foreach($attributesData as $attributeData){

            $sql = sprintf('ALTER TABLE `%s` ADD `%s` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci',
                $setup->getTable('amasty_orderattr_order_attribute_value'),
                $attributeData['attribute_code'].'_output'
            );

            $setup->getConnection()->query($sql);
        }
    }
}
