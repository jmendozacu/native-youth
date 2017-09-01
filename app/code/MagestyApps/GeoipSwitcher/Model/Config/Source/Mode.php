<?php
/**
 * Copyright © 2016 MagestyApps. All rights reserved.
 *  * See COPYING.txt for license details.
 */

namespace MagestyApps\GeoipSwitcher\Model\Config\Source;

use MagestyApps\GeoipSwitcher\Model\StoreSwitcher;

class Mode
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            StoreSwitcher::SWITCH_MODE_ONETIME => __('One Time'),
            StoreSwitcher::SWITCH_MODE_PARAM => __('On Parameter'),
            StoreSwitcher::SWITCH_MODE_ALWAYS => __('Always'),
        ];
    }
}
