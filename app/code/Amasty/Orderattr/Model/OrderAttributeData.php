<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Orderattr
 */


namespace Amasty\Orderattr\Model;

class OrderAttributeData extends \Magento\Framework\DataObject
{
    /**
     * {@inheritdoc}
     */
    public function setAttributeCode($code)
    {
        $this->setData('attribute_code', $code);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeCode()
    {
        return $this->getData('attribute_code');
    }

    /**
     * {@inheritdoc}
     */
    public function setLabel($label)
    {
        $this->setData('label', $label);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return $this->getData('label');
    }

    /**
     * {@inheritdoc}
     */
    public function setValue($value)
    {
        $this->setData('value', $value);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        return $this->getData('value');
    }

    /**
     * {@inheritdoc}
     */
    public function setValueOutput($value)
    {
        $this->setData('value_output', $value);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getValueOutput()
    {
        return $this->getData('value_output');
    }
}
