<?php
/**
 * Copyright © 2016 MagestyApps. All rights reserved.
 *  * See COPYING.txt for license details.
 */

namespace MagestyApps\GeoipSwitcher\Model;

use Magento\Customer\Model\Session;
use Magento\Framework\DataObject;
use MagestyApps\GeoipSwitcher\Helper\Database as DbHelper;
use Magento\Directory\Model\Region;

class Geoip
{
    const SESSION_PARAM_CODE = 'customer_geoip_location';

    /**
     * @var Session
     */
    private $session;

    /**
     * @var DbHelper
     */
    private $dbHelper;

    /**
     * @var Region
     */
    private $regionModel;

    /**
     * Geoip constructor.
     * @param Session $session
     * @param DbHelper $dbHelper
     * @param Region $regionModel
     */
    public function __construct(
        Session $session,
        DbHelper $dbHelper,
        Region $regionModel
    ) {
        $this->session = $session;
        $this->dbHelper = $dbHelper;
        $this->regionModel = $regionModel;
    }

    /**
     * Get current visitor's location
     *
     * @return DataObject
     */
    public function getCurrentLocation()
    {
        if (!$this->session->getData(self::SESSION_PARAM_CODE)) {
            $location = $this->getLocation();
            $this->session->setData(self::SESSION_PARAM_CODE, $location);
        }

        return new DataObject($this->session->getData(self::SESSION_PARAM_CODE));
    }

    /**
     * Get a location by IP address
     *
     * @param null $ipAddress
     * @return array
     */
    public function getLocation($ipAddress = null)
    {
        $reader = new MaxMind\Db\Reader($this->dbHelper->getDatabasePath());

        if (!$ipAddress) {
            $ipAddress = $this->dbHelper->getCustomerIp();
        }

        $result = $reader->get($ipAddress);

        $location = [];
        if (isset($result['country']) && isset($result['country']['iso_code'])) {
            $location['country'] = $result['country']['iso_code'];
        }

        if (isset($result['subdivisions'])
            && count($result['subdivisions'])
            && isset($location['country'])
        ) {
            $subdivision = reset($result['subdivisions']);
            $regionCode = $subdivision['iso_code'];
            $region = $this->regionModel->loadByCode($regionCode, $location['country']);

            if ($region && $region->getRegionId()) {
                $location['region_id'] = $region->getRegionId();
            } elseif (isset($subdivision['names']) && isset($subdivision['names']['en'])) {
                $location['region'] = $subdivision['names']['en'];
            }
        }

        if (isset($result['city'])
            && isset($result['city']['names'])
            && isset($result['city']['names']['en'])
        ) {
            $location['city'] = $result['city']['names']['en'];
        }

        if (isset($result['postal']) && isset($result['postal']['code'])) {
            $location['postcode'] = $result['postal']['code'];
        }

        return $location;
    }
}
