<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_CustomerAttributes
 */
namespace Amasty\CustomerAttributes\Controller\Adminhtml\Attribute;

use \Magento\Framework\Exception\AlreadyExistsException;
use Symfony\Component\Config\Definition\Exception\Exception;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;


class Save extends \Amasty\CustomerAttributes\Controller\Adminhtml\Attribute
{
    /**
     * @var \Magento\Catalog\Model\Product\AttributeSet\BuildFactory
     */
    protected $buildFactory;

    /**
     * @var \Magento\Framework\Filter\FilterManager
     */
    protected $filterManager;

    /**
     * @var \Magento\Catalog\Helper\Product
     */
    protected $productHelper;

    /**
     * @var \Magento\Customer\Model\AttributeFactory
     */
    protected $attributeFactory;

    /**
     * @var \Magento\Eav\Model\Adminhtml\System\Config\Source\Inputtype\ValidatorFactory
     */
    protected $validatorFactory;

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Group\CollectionFactory
     */
    protected $groupCollectionFactory;

    /**
     * @var \Magento\Eav\Setup\EavSetup $eavSetup
     */
    protected $eavSetup;

    /**
     * @var \Magento\Framework\Stdlib\DateTime $dateTime
     */
    protected $dateTime;

    /**
     * @var \Magento\Framework\App\ResourceConnection $connection
     * @todo delete from here
     */
    protected $connection;

    /**
     * @var \Magento\Framework\Translate $translate
     */
    protected $translate;
    /**
     * @var GroupRepositoryInterface
     */
    protected $groupRepository;
    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;
    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory
     */
    protected $_attrOptionCollectionFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Catalog\Model\Product\AttributeSet\BuildFactory $buildFactory
     * @param \Magento\Customer\Model\AttributeFactory $attributeFactory
     * @param \Magento\Eav\Model\Adminhtml\System\Config\Source\Inputtype\ValidatorFactory $validatorFactory
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Group\CollectionFactory $groupCollectionFactory
     * @param \Magento\Framework\Filter\FilterManager $filterManager
     * @param \Magento\Catalog\Helper\Product $productHelper
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Catalog\Model\Product\AttributeSet\BuildFactory $buildFactory,
        \Magento\Customer\Model\AttributeFactory $attributeFactory,
        \Magento\Eav\Model\Adminhtml\System\Config\Source\Inputtype\ValidatorFactory $validatorFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Group\CollectionFactory $groupCollectionFactory,
        \Magento\Framework\Filter\FilterManager $filterManager,
        \Magento\Catalog\Helper\Product $productHelper,
        \Magento\Eav\Setup\EavSetup $eavSetup,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Magento\Framework\App\ResourceConnection $connection,
        \Magento\Framework\Translate $translate,
        \Amasty\CustomerAttributes\Helper\Image $imageHelper,
        GroupRepositoryInterface $groupRepository,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder
    )
    {
        parent::__construct($context, $coreRegistry, $resultPageFactory);
        $this->translate = $translate;
        $this->connection = $connection;
        $this->buildFactory = $buildFactory;
        $this->filterManager = $filterManager;
        $this->productHelper = $productHelper;
        $this->attributeFactory = $attributeFactory;
        $this->validatorFactory = $validatorFactory;
        $this->groupCollectionFactory = $groupCollectionFactory;
        $this->eavSetup = $eavSetup;
        $this->dateTime = $dateTime;
        $this->_imageHelper = $imageHelper;
        $this->groupRepository = $groupRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->_attrOptionCollectionFactory = $attrOptionCollectionFactory;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = null;
        if ($data) {
            $addToSet = false;
            $frontendInput = (isset($data['frontend_input'])) ? $data['frontend_input'] : '';
            $redirectBack = $this->getRequest()->getParam('back', false);
            /* @var $model \Magento\Catalog\Model\ResourceModel\Eav\Attribute */
            $model = $this->attributeFactory->create();

            if (isset($data['attribute_id'])) {
                $id = $data['attribute_id'];
                $model->load($id);

                // entity type check
                if ($model->getEntityTypeId() != $this->_entityTypeId) {
                    $this->messageManager->addError(__('You cannot update this attribute'));
                    return $resultRedirect->setPath('*/*/', ['_current' => true]);
                }

                $data['attribute_code'] = $model->getAttributeCode();
                $data['is_user_defined'] = $model->getIsUserDefined();
                $data['frontend_input'] = $model->getFrontendInput();
                $data['type_internal']  = $model->getTypeInternal();
            }
            if($data['is_used_in_grid']){
                $data['is_visible_in_grid'] =
                    $data['is_filterable_in_grid'] =
                    $data['is_searchable_in_grid'] =
                    $data['is_filterable_in_search'] = 1;
            }

            $data['is_configurable'] = isset($data['is_configurable']) ?
                $data['is_configurable'] : 0;

            if (is_null($model->getIsUserDefined())
                || $model->getIsUserDefined() != 0
            ) {
                $data['backend_type']
                    = $model->getBackendTypeByInput($data['frontend_input']);
            }

            $defaultValueField = $model->getDefaultValueByInput(
                $data['frontend_input']
            );
            if(!$defaultValueField && 'statictext' == $data['frontend_input']) {
                $defaultValueField = 'default_value_textarea';
            }
            if ($defaultValueField) {
                $data['default_value'] = $this->getRequest()->getParam(
                    $defaultValueField
                );
            }

            if ($data['is_required'] == 2) {
                $data['required_on_front'] = 1;
                $data['is_required'] = 0;
            } else {
                $data['required_on_front'] = 0;
            }

            if (is_null($model->getIsUserDefined())
                || $model->getIsUserDefined() != 0
            ) {
                $data['backend_type'] = $model->getBackendTypeByInput($data['frontend_input']);
            }

            if (!isset($data['apply_to'])) {
                $data['apply_to'] = array();
            }

            $data = $this->setSourceModel($data);

            if (!empty($data['customer_groups'])) {
                $data['customer_groups'] = implode(',',
                    $data['customer_groups']);
            }
            else{
                $data['customer_groups'] = '';
            }

            $defaultValueField = $model->getDefaultValueByInput($data['frontend_input']);
            if ($defaultValueField) {
                $data['default_value'] = $data[$defaultValueField];
            }

            $data['store_ids'] = '';
            $data['sort_order'] = $data['sorting_order'] + 1000;//move attributes to the bottom

            if ($data['stores']) {
                if (is_array($data['stores'])) {
                    $data['store_ids'] = implode(',', $data['stores']);
                } else {
                    $data['store_ids'] = $data['stores'];
                }
                unset($data['stores']);
            }

            $model->addData($data);
            $isNewCustomerGroupOptions = $this->_addOptionsForCustomerGroupAttribute($model);
            if (!$id) {
                $model->setEntityTypeId($this->_entityTypeId);
                $model->setIsUserDefined(1);
                $addToSet = true;
            }


            if ($this->getRequest()->getParam('set')
                && $this->getRequest()->getParam('group')
            ) {
                // For creating product attribute on product page we need specify attribute set and group
                $model->setAttributeSetId($this->getRequest()->getParam('set'));
                $model->setAttributeGroupId(
                    $this->getRequest()->getParam('group')
                );
            }
            $usedInForms = $this->getUsedFroms($model);
            $model->setData('used_in_forms', $usedInForms);
            try {
                $this->_prepareForSave();
                $model->save();
                if (('multiselectimg' === $data['frontend_input'] || 'selectimg' === $data['frontend_input'])
                    && array_key_exists('default', $data)
                    && is_array($data['default'])
                ) {
                    $this->_saveDefaultValue($model, $data['default']);
                }
                if ($isNewCustomerGroupOptions) {
                    $this->_saveCustomerGroupIds($model);
                }

                $this->_eventManager->dispatch('customer_attributes_after_save');
                if ($addToSet) {
                    $attrSetId = $this->_objectManager->get('Magento\Customer\Model\Customer')
                        ->getResource()->getEntityType()
                        ->getDefaultAttributeSetId();
                    $this->eavSetup->addAttributeToSet(
                        'customer', $attrSetId, 'General',
                        $model->getAttributeCode()
                    );
                }

                $this->messageManager->addSuccess(__('Customer attribute was successfully saved'));

                $this->_session->setAttributeData(false);
                if ($this->getRequest()->getParam('popup')) {
                    $requestParams = [
                        'attributeId' => $this->getRequest()->getParam('product'),
                        'attribute' => $model->getId(),
                        '_current' => true,
                        'product_tab' => $this->getRequest()->getParam('product_tab'),
                    ];
                    $resultRedirect->setPath('catalog/product/addAttribute', $requestParams);
                } elseif ($redirectBack) {
                    $resultRedirect->setPath('*/*/edit', ['attribute_id' => $model->getId(), '_current' => true]);
                } else {
                    $resultRedirect->setPath('*/*/');
                }
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
               $resultRedirect->setPath('amcustomerattr/*/edit', ['attribute_id' => $id, '_current' => true]);
            }

        } else {
            $resultRedirect->setPath('*/*/');
        }
        return $resultRedirect;
    }

    protected function _addOptionsForCustomerGroupAttribute(&$model){
        $data = $model->getData();
        if(( (array_key_exists('type_internal', $data) && $data['type_internal'] == 'selectgroup')
            || (array_key_exists('frontend_input', $data) && $data['frontend_input'] == 'selectgroup')
            )
            && !array_key_exists('option', $data)
        ) {
            $values = [
                'order' => [],
                'value' => []
            ];
            $customerGroups = $this->groupRepository->getList($this->searchCriteriaBuilder->create())->getItems();
            $i = 0;
            foreach ($customerGroups as $item) {
                $name = 'option_' . $i++;
                $values['value'][$name] = [
                    0 => $item->getCode()
                ];
                $values['order'][$name] = $item->getId();
                $values['group_id'][$name] = $item->getId();
            }
            array_shift($values['value']);
            array_shift($values['order']);
            array_shift($values['group_id']);
            $data['option'] = $values;
            $model->setData($data);

            return true;
        }
        return false;
    }

    protected function getUsedFroms($attribute){
        $usedInForms = [
            'adminhtml_customer',
            'amasty_custom_attribute'
        ];
        if($attribute->getIsVisibleOnFront() == '1'){
            $usedInForms[] = 'customer_account_edit';
        }
        if($attribute->getOnRegistration() == '1'){
            $usedInForms[] = 'customer_account_create';
            $usedInForms[] = 'customer_attributes_registration';
        }
        if($attribute->getUsedInProductListing()){
            $usedInForms[] = 'adminhtml_checkout';
            $usedInForms[] = 'customer_attributes_checkout';

        }
        return $usedInForms;
    }

    /**
     * @param $data
     * @return mixed
     */
    protected function setSourceModel($data)
    {
        if (array_key_exists('type_internal', $data)
            && $data['type_internal'] == 'selectgroup') {
            $data['frontend_input'] = 'selectgroup';
        }
        switch ($data['frontend_input']) {
            case 'boolean':
                $data['source_model']
                    = 'Magento\Eav\Model\Entity\Attribute\Source\Boolean';
                break;
            case 'multiselectimg':
            case 'selectimg' :
                $data['data_model'] = 'Amasty\CustomerAttributes\Model\Eav\Attribute\Data\\' . ucfirst($data['frontend_input']);
                $data['backend_type'] = 'varchar';
            case 'select':
            case 'checkboxes':
            case 'multiselect':
            case 'radios':
                $data['source_model']
                    = 'Magento\Eav\Model\Entity\Attribute\Source\Table';
                $data['backend_model']
                    = 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend';
                break;
            case 'file':
                $data['type_internal'] = 'file';
                $data['backend_type'] = 'varchar';
                break;
            case 'statictext':
                $data['type_internal'] = 'statictext';
                $data['backend_type'] = 'text';
                $data['data_model'] = 'Amasty\CustomerAttributes\Model\Eav\Attribute\Data\\' . ucfirst($data['frontend_input']);
                break;
            case 'selectgroup':
                $data['type_internal'] = 'selectgroup';
                $data['frontend_input']= 'select';
                $data['source_model']
                    = 'Magento\Eav\Model\Entity\Attribute\Source\Table';
                $data['backend_model']
                    = 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend';
                //$data['backend_type'] = 'varchar';
                break;
        }

        return $data;
    }

    protected function _saveDefaultValue($object, $defaultValue)
    {
        if ($defaultValue !== null) {
            $bind = ['default_value' => implode(',', $defaultValue)];
            $where = ['attribute_id = ?' => $object->getId()];
            $this->connection->getConnection()->update($this->connection->getTableName('eav_attribute'), $bind, $where);
        }
    }

    protected function _saveCustomerGroupIds($model)
    {
        $data = $model->getData();
        if($data['type_internal'] == 'selectgroup'
                || $data['frontend_input'] == 'selectgroup'
        ) {
            $options = $this->_attrOptionCollectionFactory->create()->setAttributeFilter(
                $model->getId()
            )->setPositionOrder(
                'asc',
                true
            )->load();

            $customerGroups = $this->groupRepository->getList($this->searchCriteriaBuilder->create())->getItems();
            $i = 1;
            foreach($options as $option) {
                if(array_key_exists($i, $customerGroups)) {
                    $group = $customerGroups[$i++];
                    if($group->getCode() == $option->getValue()) {
                        $option->setGroupId($group->getId());
                        $option->save();
                    }
                }
            }
        }
    }

    protected function _prepareForSave()
    {
        /**
         * Deleting
         */
        $toDelete = $this->getRequest()->getParam('amcustomerattr_icon_delete');
        if ($toDelete) {
            foreach ($toDelete as $optionId => $del) {
                if ($del) {
                    $this->_imageHelper->delete($optionId);
                }
            }
        }

        /**
         * Uploading files
         */
        $files = $this->getRequest()->getFiles('amcustomerattr_icon');
        if ($files) {
            foreach ($files as $optionId => $file) {
                if (UPLOAD_ERR_OK == $file['error']) {
                    $this->_imageHelper->uploadImage($optionId);
                }
            }
        }
    }
}
