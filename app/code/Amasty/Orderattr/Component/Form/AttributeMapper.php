<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Orderattr
 */


namespace Amasty\Orderattr\Component\Form;

use \Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class AttributeMapper extends \Magento\Ui\Component\Form\AttributeMapper
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $localeDate;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    public function __construct(
        TimezoneInterface $localeData,
        \Magento\Framework\ObjectManagerInterface $objectManager
    )
    {
        $this->localeDate = $localeData;
        $this->_objectManager = $objectManager;
    }

    /**
     * Form element mapping
     *
     * @var array
     */
    private $formElementMap = [
        'text' => 'input',
        'hidden' => 'input',
        'boolean' => 'select',
    ];

    /**
     * EAV attribute properties to fetch from meta storage
     * @var array
     */
    private $metaPropertiesMap = [
        'dataType' => 'getFrontendInput',
        'visible' => 'getIsVisibleOnFront',
        'required' => 'getIsFrontRequired',
        'label' => 'getStoreLabel',
        'sortOrder' => 'getSortingOrder',
        'notice' => 'getNote',
        'default' => 'getDefaultOrLastValue',
        'frontend_class' => 'getFrontendClass',
        'size' => 'getMultilineCount',
        'validate_length_count' => 'getValidateLengthCount'

    ];

    /**
     * Get attributes meta
     *
     * @param \Magento\Eav\Api\Data\AttributeInterface $attribute
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function map($attribute)
    {
        foreach ($this->metaPropertiesMap as $metaName => $methodName) {
            $value = $attribute->$methodName();
            $meta[$metaName] = $value;
            if ('getFrontendInput' === $methodName) {
                $meta['formElement'] = isset($this->formElementMap[$value])
                    ? $this->formElementMap[$value]
                    : $value;
            } elseif('getStoreLabel' == $methodName) {
                $meta[$metaName] = __($meta[$metaName]);
            }
        }
        if ($attribute->usesSource()) {
            $displayEmptyOption = $this->displayEmptyOption($attribute);
            $allOptions = $attribute->getSource()->getAllOptions(
                $displayEmptyOption
            );
            foreach($allOptions as $key => $option) {
                if($option['label'] == "") {
                    $allOptions[$key]['label'] = " ";
                }
                break;
            }
            $meta['options'] = $allOptions;
        }
        
        $rules = [];
        if (isset($meta['required']) && $meta['required'] == 1) {
            $rules['required-entry'] = true;
        }
        if (isset($meta['frontend_class'])) {
            if ($meta['frontend_class'] == 'validate-length') {
                $maxLength = (array_key_exists('validate_length_count', $meta))? $meta['validate_length_count'] : 25;
                $rules[$meta['frontend_class']] = 'maximum-length-' . $maxLength;
                $rules['max_text_length'] = $maxLength;
            }
            else{
                $rules[$meta['frontend_class']] = true;
            }
        }
        
        $meta['validation'] = $rules;
        if ($elementTmpl = $this->getElementTmpl($attribute->getFrontendInput())) {
            $meta['config']['elementTmpl'] = $elementTmpl;
        }
        if ($attribute->getFrontendInput() == 'datetime') {
            $meta['options'] = [
                'showsTime' => true,
                'timeFormat' => $this->localeDate->getTimeFormat(),
            ];
        }
        if ($attribute->getFrontendInput() == 'boolean') {
            $meta['options'] = $this->_objectManager->get('Amasty\Orderattr\Block\Data\Form\Element\Boolean')->getValues();
        }

        $meta['shipping_methods'] = $attribute->getShippingMethods()
            ? explode(',', $attribute->getShippingMethods())
            : [];

        return $meta;
    }

    /**
     * @param \Amasty\Orderattr\Model\ResourceModel\Eav\Attribute $attribute
     *
     * @return boolean
     */
    protected function displayEmptyOption($attribute)
    {
        switch ($attribute->getFrontendInput()) {
            case 'radios':
            case 'checkboxes':
                $displayEmptyOption = false;
                break;
            default:
                $displayEmptyOption = true;
                break;
        }

        return $displayEmptyOption;

    }

    /**
     * @param string $attributeFrontendInput
     *
     * @return string
     */
    protected function getElementTmpl($attributeFrontendInput)
    {
        switch ($attributeFrontendInput) {
            case 'radios':
                $elementTmpl = 'Amasty_Orderattr/form/element/radios';
                break;
            case 'checkboxes':
                $elementTmpl = 'Amasty_Orderattr/form/element/checkboxes';
                break;
            case 'datetime':
                $elementTmpl = 'Amasty_Orderattr/form/element/datetime';
                break;
            default:
                $elementTmpl = '';
                break;
        }

        return $elementTmpl;
    }

}
