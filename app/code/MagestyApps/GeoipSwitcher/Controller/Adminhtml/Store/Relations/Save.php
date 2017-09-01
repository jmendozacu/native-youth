<?php
/**
 * Copyright © 2016 MagestyApps. All rights reserved.
 *  * See COPYING.txt for license details.
 */

namespace MagestyApps\GeoipSwitcher\Controller\Adminhtml\Store\Relations;

use Magento\Backend\App\Action;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\StoreFactory;

class Save extends Action
{
    /**
     * @var StoreFactory
     */
    private $storeFactory;

    /**
     * Save constructor.
     * @param Action\Context $context
     * @param StoreFactory $storeFactory
     */
    public function __construct(Action\Context $context, StoreFactory $storeFactory)
    {
        parent::__construct($context);

        $this->storeFactory = $storeFactory;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('MagestyApps_GeoipSwitcher::store_relations_save');
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        try {
            $data = $this->getRequest()->getPost();
            if (!isset($data['countries']) || !is_array($data['countries'])) {
                throw new LocalizedException(__('No data to save'));
            }

            foreach ($data['countries'] as $storeId => $countries) {
                $store = $this->storeFactory->create()->load($storeId);
                if (!$store || !$store->getId()) {
                    throw new LocalizedException(__('Store with id %1 no longer exists', $storeId));
                }
                $countries = implode(',', $countries);

                $store->setStoreswitcherCountries($countries)->save();
            }

            $this->messageManager->addSuccess(__('Store-Country relations have been successfully saved'));
        } catch (\Exception $e) {
            $this->messageManager->addError(__('An error occurred while saving relations: %1', $e->getMessage()));
        }

        return $resultRedirect->setRefererUrl();
    }
}
