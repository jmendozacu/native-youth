<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Orderattr
 */


namespace Amasty\Orderattr\Controller\Adminhtml\Order\Attributes;


use Magento\Backend\App\Action;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Psr\Log\LoggerInterface;

class Save extends \Magento\Sales\Controller\Adminhtml\Order
{

    public function execute()
    {
        $orderId 		 = $this->getRequest()->getParam('order_id');
        $resultRedirect  = $this->resultRedirectFactory->create();
        $data        	 = $this->getRequest()->getParams();
        if ($data) {
            try {
                if (!is_array($data)) {
                    $data = $data->toArray();
                }
                $this->getOrderAttributesManagement()->saveOrderAttributes(
                    $orderId, $data, 'is_visible_on_front'
                );
                $this->messageManager->addSuccess(__('The order attributes have been updated.'));
            } catch (\Exception $e) {
                $this->messageManager->addException(
                    $e,
                    __('An error occurred while updating the order attributes.')
                );
            }
        }
        if ($orderId) {
            $resultRedirect->setPath('sales/order/view',
                ['order_id' => $orderId, '_current' => true]);
        } else {
            $resultRedirect->setPath('sales/order/',
                ['_current' => true]);
        }
        return $resultRedirect;
    }

    /**
     * @return \Amasty\Orderattr\Model\OrderAttributesManagement
     */
    protected function getOrderAttributesManagement()
    {
        return $this->_objectManager->create('Amasty\Orderattr\Model\OrderAttributesManagement');
    }
}
