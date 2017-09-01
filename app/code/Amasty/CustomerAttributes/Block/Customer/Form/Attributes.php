<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_CustomerAttributes
 */



namespace Amasty\CustomerAttributes\Block\Customer\Form;
use Magento\Framework\Data\Form\Element\Factory;

use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;

class Attributes extends \Magento\Catalog\Block\Adminhtml\Form
{
    /**
     * @var \Magento\Customer\Model\AttributeMetadataDataProvider
     */
    protected $attributeMetadataDataProvider;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var Factory
     */
    protected $_factoryElement;

    protected $_customerData;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Prepare attributes form
     *
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = [],
        \Magento\Customer\Model\AttributeMetadataDataProvider $attributeMetadataDataProvider,
        \Amasty\CustomerAttributes\Block\Widget\Form\Renderer\Fieldset $fieldsetRenderer,
        \Amasty\CustomerAttributes\Block\Widget\Form\Renderer\Element  $elementRenderer,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        GroupRepositoryInterface $groupRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Factory $factoryElement
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->attributeMetadataDataProvider = $attributeMetadataDataProvider;
        $this->_factoryElement = $factoryElement;
        $this->_fieldsetRenderer = $fieldsetRenderer;
        $this->_elementRenderer = $elementRenderer;
        $this->_objectManager = $objectManager;
        $this->_groupRepository = $groupRepository;
        $this->_searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->storeManager = $context->getStoreManager();
    }

    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $fieldset = $form->addFieldset(
            'group-fields-customer-attributes',
            ['class'  => 'user-defined',
             'legend' => __('Additional Settings')
            ]
        );

        $attributes = $this->attributeMetadataDataProvider->loadAttributesCollection(
            'customer',
            'customer_attributes_registration'
        );

        if (!$attributes){
            return;
        }
        $fieldset->setRenderer($this->_fieldsetRenderer);

        $customerSession = $this->_objectManager->get('Magento\Customer\Model\Session');
        $this->_customerData = $customerSession->isLoggedIn()?
            $customerSession->getCustomer()->getData():
            [];

        $this->_setFieldset($attributes, $fieldset, ['gallery']);

        if($this->_customerData) {
           $form->addValues($this->_customerData);
        }

        $this->setForm($form);

    }

    /**
     * Retrieve additional element types
     *
     * @return array
     */
    protected function _getAdditionalElementTypes()
    {
        $result = [
            'price' => 'Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Price',
            'weight' => 'Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Weight',
            'gallery' => 'Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Gallery',
            'image' => 'Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Image',
            'boolean' => 'Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Boolean',
            'textarea' => 'Magento\Catalog\Block\Adminhtml\Helper\Form\Wysiwyg',
        ];

        $response = new \Magento\Framework\DataObject();
        $response->setTypes([]);
        $this->_eventManager->dispatch('adminhtml_catalog_product_edit_element_types', ['response' => $response]);

        foreach ($response->getTypes() as $typeName => $typeClass) {
            $result[$typeName] = $typeClass;
        }

        return $result;
    }

    /**
     * Check whether attribute is visible
     *
     * @param \Magento\Eav\Model\Entity\Attribute $attribute
     * @return bool
     */
    protected function _isAttributeVisible(\Magento\Eav\Model\Entity\Attribute $attribute)
    {
        $blockName = $this->getNameInLayout();
        if ($blockName == 'attribute_customer_register') {
            $check = $attribute->getData('on_registration') == '1';
        }
        else {
            $check = $attribute->getData('is_visible_on_front') == '1' &&
                (   !($attribute->getAccountFilled() == '1'
                        && array_key_exists($attribute->getAttributeCode(), $this->_customerData)
                    )
                    || $attribute->getAccountFilled() == '0'
                );
        }

        $store = $this->storeManager->getStore()->getId();
        $stores = $attribute->getStoreIds();
        $stores = explode(',', $stores);

        return !(!$attribute || $attribute->hasIsVisible() && !$attribute->getIsVisible())
                && $check
                && in_array($store, $stores);
    }

    /**
     * Set Fieldset to Form
     *
     * @param array $attributes attributes that are to be added
     * @param \Magento\Framework\Data\Form\Element\Fieldset $fieldset
     * @param array $exclude attributes that should be skipped
     * @return void
     */
    protected function _setFieldset($attributes, $fieldset, $exclude = [])
    {
        $this->_addElementTypes($fieldset);
        foreach ($attributes as $attribute) {
            /* @var $attribute \Magento\Eav\Model\Entity\Attribute */
            if (!$this->_isAttributeVisible($attribute)) {
                continue;
            }
            $attribute->setStoreId($this->_storeManager->getStore()->getId());
            if (($inputType = $attribute->getFrontend()->getInputType()) && !in_array(
                    $attribute->getAttributeCode(),
                    $exclude
                ) && ('media_image' != $inputType || $attribute->getAttributeCode() == 'image')
            ) {
                $typeInternal = $attribute->getTypeInternal();

                $inputTypes = [
                    'statictext'  => 'note',
                    'selectgroup' => 'select'
                ];

                if ($typeInternal) {
                    $inputType = isset($inputTypes[$typeInternal])
                        ? $inputTypes[$typeInternal] : $typeInternal;
                }
                $fieldType = $inputType;
                $rendererClass = $attribute->getFrontend()->getInputRendererClass();
                if (!empty($rendererClass)) {
                    $fieldType = $inputType . '_' . $attribute->getAttributeCode();
                    $fieldset->addType($fieldType, $rendererClass);
                }
                $fieldType = 'Amasty\CustomerAttributes\Block\Data\Form\Element\\' . ucfirst($fieldType);

                $data =  [
                    'name' => $attribute->getAttributeCode(),
                    'label' => $attribute->getFrontend()->getLocalizedLabel(),
                    'class' => $attribute->getFrontend()->getClass(),
                    'required' => $attribute->getIsRequired() || $attribute->getRequiredOnFront(),
                    'note' => $attribute->getNote()
                ];
                if($typeInternal == 'selectgroup'
                    && !$this->_scopeConfig->getValue('amcustomerattr/general/allow_change_group')
                    && array_key_exists($attribute->getAttributeCode(), $this->_customerData)
                ) {
                    $data['disabled'] = 'disabled';
                }

                $element = $fieldset->addField(
                    $attribute->getAttributeCode(),
                    $fieldType,
                    $data
                )->setEntityAttribute(
                    $attribute
                );

                $element->setValue( $attribute->getDefaultValue());

                $element->setRenderer($this->_elementRenderer);

                $element->setAfterElementHtml($this->_getAdditionalElementHtml($element));

                $this->_applyTypeSpecificConfig($inputType, $element, $attribute);

                if($inputType == 'multiselectimg' || $inputType == 'selectimg') {
                    $element->addElementValues($attribute->getSource()->getAllOptions(false));
                }
            }
        }
    }
}
