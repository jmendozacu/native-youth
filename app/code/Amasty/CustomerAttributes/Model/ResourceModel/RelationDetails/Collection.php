<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_CustomerAttributes
 */
namespace Amasty\CustomerAttributes\Model\ResourceModel\RelationDetails;

use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    public function getByRelation($relationId)
    {
        $this->getSelect()
            ->where('relation_id = ?', $relationId);
        return $this;
    }

    protected function _construct()
    {
        $this->_init(
            'Amasty\CustomerAttributes\Model\RelationDetails',
            'Amasty\CustomerAttributes\Model\ResourceModel\RelationDetails'
        );
    }

}