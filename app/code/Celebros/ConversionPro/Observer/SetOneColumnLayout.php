<?php
/**
 * Celebros
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish correct extension functionality.
 * If you wish to customize it, please contact Celebros.
 *
 ******************************************************************************
 * @category    Celebros
 * @package     Celebros_ConversionPro
 */
namespace Celebros\ConversionPro\Observer;

use Magento\Framework\Event\ObserverInterface;

class SetOneColumnLayout implements ObserverInterface
{
    /**
     * @var \Magento\Framework\App\Action\Context
     */
    protected $context;
    
    /**
     * @var \Celebros\ConversionPro\Helper\Data
     */
    protected $helper;
    
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Celebros\ConversionPro\Helper\Data $helper)
    {
        $this->context = $context;
        $this->helper = $helper;
    }
    
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->helper->isEnabledOnFrontend())
            return;
        
        $view = $this->context->getView();
        $page = $view->getPage();
        
        $layoutUpdate = $page->getLayout()->getUpdate();
        $layoutUpdate->addHandle('conversionpro_catalogsearch_result_index');
        
        $page->getConfig()->setPageLayout('1column');
        
    }
    
}