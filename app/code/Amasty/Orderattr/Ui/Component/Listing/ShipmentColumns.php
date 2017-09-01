<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Orderattr
 */

namespace Amasty\Orderattr\Ui\Component\Listing;

/**
 * Class Columns
 */
class ShipmentColumns extends Columns
{
    public function allowToAddAttributes()
    {
        return (boolean) $this->config->getShowShipmentGrid();
    }
}
