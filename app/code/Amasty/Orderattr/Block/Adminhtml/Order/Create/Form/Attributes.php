<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Orderattr
 */

namespace Amasty\Orderattr\Block\Adminhtml\Order\Create\Form;

use \Amasty\Orderattr\Block\Adminhtml\Order\View\Attributes\Edit\Form;


class Attributes extends Form
{

    protected function _prepareForm()
    {
        $this->setFieldNameSuffix('order[attributes]');
        return parent::_prepareForm();
    }

    /**
     * @inheritdoc
     */
    protected function createAttributesFieldSet($form)
    {
        $fieldset = $form->addFieldset('base_fieldset',[]);
        return $fieldset;
    }

    /**
     * @return \Amasty\Orderattr\Model\Order\Attribute\Value
     */
    protected function getOrderAttributeValues()
    {
        /**
         * @var \Amasty\Orderattr\Model\Order\Attribute\Value $orderAttributes
         */
        return $this->orderAttributesValuesFactory->create();

    }

    /**
     * @return \Amasty\Orderattr\Model\ResourceModel\Order\Attribute\Collection
     */
    protected function getOrderEavAttributes()
    {
        return $this->attributeMetadataDataProvider
            ->loadAttributesForCreateOrderFormByStoreId($this->getStoreId());
    }

    /**
     * @return int
     */
    protected function getStoreId()
    {
        if ($this->getStore()) {
            $storeId = $this->getStore()->getId();
        } else {
            $storeId = $this->_storeManager->getDefaultStoreView()->getId();
        }

        return $storeId;
    }

    public function toHtml()
    {
        $this->_beforeToHtml();
        return parent::toHtml();
       // return $this->getForm()->getElement('base_fieldset')->toHtml();
    }

}
