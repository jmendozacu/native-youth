<?php
/**
 * Copyright © 2016 MagestyApps. All rights reserved.
 *  * See COPYING.txt for license details.
 */

namespace MagestyApps\GeoipSwitcher\Model\Config\Source;

use MagestyApps\GeoipSwitcher\Model\StoreSwitcher;

class Scope
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            StoreSwitcher::SWITCH_SCOPE_GLOBAL => __('Global'),
            StoreSwitcher::SWITCH_SCOPE_WEBSITE => __('Website'),
            StoreSwitcher::SWITCH_SCOPE_STORE => __('Store Group'),
        ];
    }
}
