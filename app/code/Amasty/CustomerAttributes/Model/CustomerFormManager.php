<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_CustomerAttributes
 */


namespace Amasty\CustomerAttributes\Model;
use \Magento\Framework\Model\AbstractModel;
class CustomerFormManager extends AbstractModel
{
    public function __construct()
    {
        parent::_construct();
        $this->_init('Amasty\CustomerAttributes\Model\ResourceModel\CustomerFormManager');
    }


}