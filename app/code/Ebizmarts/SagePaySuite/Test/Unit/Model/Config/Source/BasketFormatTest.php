<?php
/**
 * Copyright © 2015 ebizmarts. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Ebizmarts\SagePaySuite\Test\Unit\Model\Config\Source;

class BasketFormatTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Ebizmarts\SagePaySuite\Model\Config\Source\AvsCvc
     */
    private $basketFormatModel;

    // @codingStandardsIgnoreStart
    protected function setUp()
    {
        $objectManagerHelper = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->basketFormatModel = $objectManagerHelper->getObject(
            'Ebizmarts\SagePaySuite\Model\Config\Source\BasketFormat',
            []
        );
    }
    // @codingStandardsIgnoreEnd

    public function testToOptionArray()
    {
        $this->assertEquals(
            [
                'value' => \Ebizmarts\SagePaySuite\Model\Config::BASKETFORMAT_SAGE50,
                'label' => __('Sage50 compatible')
            ],
            $this->basketFormatModel->toOptionArray()[0]
        );
    }
}