<?php
/**
 * Copyright © 2016 MagestyApps. All rights reserved.
 *  * See COPYING.txt for license details.
 */

namespace MagestyApps\GeoipSwitcher\Controller\Adminhtml\Database;

use Magento\Backend\App\Action;
use MagestyApps\GeoipSwitcher\Model\MaxMind\DbUpdate;

class RunUpdate extends Action
{
    /**
     * @var DbUpdate
     */
    private $dbUpdateModel;

    /**
     * RunUpdate constructor.
     * @param Action\Context $context
     * @param DbUpdate $dbUpdateModel
     */
    public function __construct(
        Action\Context $context,
        DbUpdate $dbUpdateModel
    ) {
        parent::__construct($context);

        $this->dbUpdateModel = $dbUpdateModel;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('MagestyApps_GeoipSwitcher::magestyapps_geoip');
    }

    /**
     * RunUpdate action
     */
    public function execute()
    {
        $step = $this->getRequest()->getParam('step', false);
        $nextStep = false;
        $success = true;
        $error = false;

        $dbModel = $this->dbUpdateModel;

        try {
            if ($step == 'start') {
                $result['text'] = __('Downloading database archive');
                $nextStep = 'download';
            } elseif ($step == 'download') {
                $dbModel->download();

                $result['text'] = __('Creating current database backup');
                $nextStep = 'backup';
            } elseif ($step == 'backup') {
                $dbModel->createBackup();
                $result['text'] = __('Uncompressing archive');
                $nextStep = 'unpack';
            } elseif ($step == 'unpack') {
                $dbModel->extract();
                $result['text'] = __('Deleting temporary files');
                $nextStep = 'delete';
            } elseif ($step == 'delete') {
                unlink($dbModel->getUpdateDestination());

                $result['text'] = __('Finished');
                $result['stop'] = true;
                $result['url'] = '';
            }
        } catch (\Exception $e) {
            $success = false;
            $error = $e->getMessage();
        }

        if ($nextStep) {
            $result['url'] = $this->getUrl('*/*/runUpdate', ['step' => $nextStep]);
        }

        if (!$success) {
            $result['error'] = true;
            $result['text'] = $error ? $error : __('An error occured while updating GeoIP database');
            $result['stop']  = true;
            $result['url']   = '';
        }

        $this->getResponse()->setBody(json_encode($result));
    }
}
