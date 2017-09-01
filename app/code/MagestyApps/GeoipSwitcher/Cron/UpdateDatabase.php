<?php
/**
 * Copyright © 2016 MagestyApps. All rights reserved.
 *  * See COPYING.txt for license details.
 */

namespace MagestyApps\GeoipSwitcher\Cron;

use MagestyApps\GeoipSwitcher\Model\MaxMind\DbUpdate;

class UpdateDatabase
{
    /**
     * @var DbUpdate
     */
    private $dbUpdate;

    /**
     * UpdateDatabase constructor.
     *
     * @param DbUpdate $dbUpdate
     */
    public function __construct(
        DbUpdate $dbUpdate
    ) {
        $this->dbUpdate = $dbUpdate;
    }

    /**
     * Delete all product flat tables for not existing stores
     *
     * @return void
     */
    public function execute()
    {
        $this->dbUpdate->download();
        //$this->dbUpdate->createBackup();
        $this->dbUpdate->extract();

        unlink($this->dbUpdate->getUpdateDestination());
    }
}
