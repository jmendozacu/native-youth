<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_CustomerAttributes
 */
namespace Amasty\CustomerAttributes\Model\ResourceModel;

use \Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class RelationDetails extends AbstractDb
{
    public function _construct()
    {
        $this->_init('amasty_customer_attributes_details', 'id');
    }

    public function saveDetails($data)
    {
        $relationId = $data['relation_id'];
        $attributeIds = $data['attribute_id'];
        $optionIds = $data['option_id'];
        $dependentIds = $data['dependend_attribute_id'];

        $relationDetailsTable = $this->getTable('amasty_customer_attributes_details');

        /*
         * Delete data for relation first
         */
        $clearCondition = array(
            'relation_id = ?' => $relationId
        );
        $this->getConnection()->delete(
            $relationDetailsTable, $clearCondition
        );

        /*
         * Insert new data
         */
        $insertData = array();
        foreach ($attributeIds as $attr) {
            foreach ($optionIds as $option) {
                foreach ($dependentIds as $dep) {
                    $insertData[] = array(
                        'relation_id' => $relationId,
                        'option_id' => $option,
                        'dependent_attribute_id' => $dep,
                        'attribute_id' => $attr,
                    );
                }
            }
        }

        if (count($insertData)) {
            $this->getConnection()->insertMultiple(
                $relationDetailsTable, $insertData
            );
        }
    }

    public function fastDelete($ids)
    {
        $db = $this->getConnection();
        $table = $this->getTableName('amasty_customer_attributes_details');
        $db->delete($table, $db->quoteInto('id IN(?)', $ids));
    }
}