<?php
/**
 * Copyright © 2016 MagestyApps. All rights reserved.
 *  * See COPYING.txt for license details.
 */

namespace MagestyApps\GeoipSwitcher\Controller\Adminhtml\Store\Relations;

use Magento\Backend\App\Action;

class Index extends Action
{
    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('MagestyApps_GeoipSwitcher::store_relations');
    }

    public function execute()
    {
        $this->_view->loadLayout();

        $pageTitle = __('Manage Store-Country Relations');
        $this->_view->getLayout()
            ->getBlock('page.title')
            ->setPageTitle($pageTitle);

        $this->_view->renderLayout();
    }
}
