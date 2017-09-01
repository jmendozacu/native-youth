<?php
/**
 * Copyright © 2016 MagestyApps. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace MagestyApps\GeoipSwitcher\Block\Adminhtml\StoreSwitcher;

use Magento\Backend\Block\Template;
use Magento\Directory\Model\Config\Source\Country;

class Relations extends Template
{
    protected $_template = 'storeswitcher/relations.phtml';

    /**
     * @var Country
     */
    private $countries;

    /**
     * Relations constructor.
     *
     * @param Template\Context $context
     * @param Country $countries
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Country $countries,
        array $data = []
    ) {
        $this->countries = $countries;
        parent::__construct($context, $data);
    }

    /**
     * Get all existing websites
     *
     * @return array|\Magento\Store\Api\Data\WebsiteInterface[]
     */
    public function getAllWebsites()
    {
        return $this->_storeManager->getWebsites();
    }

    /**
     * Get array of all countries with codes
     *
     * @return array
     */
    public function getAllCountries()
    {
        return $this->countries->toOptionArray(true);
    }

    /**
     * Get form url to save relations
     *
     * @return string
     */
    public function getFormActionUrl()
    {
        return $this->getUrl('*/*/save');
    }

    /**
     * Check whether "save" action is allowed
     *
     * @return bool
     */
    public function isSaveAllowed()
    {
        return $this->_authorization->isAllowed('MagestyApps_GeoipSwitcher::store_relations_save');
    }
}
