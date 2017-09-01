<?php
/**
 * Copyright © 2016 MagestyApps. All rights reserved.
 *  * See COPYING.txt for license details.
 */

namespace MagestyApps\GeoipSwitcher\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Relations extends Field
{
    /**
     * Adds update button to config field
     *
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $url = $this->_urlBuilder->getUrl('magestyapps_geoip/store_relations/index');
        $buttonHtml = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')
            ->setType('button')
            ->setLabel(__('Manage Relations'))
            ->setOnClick("window.open('".$url."')")
            ->toHtml();
        return $buttonHtml;
    }
}
