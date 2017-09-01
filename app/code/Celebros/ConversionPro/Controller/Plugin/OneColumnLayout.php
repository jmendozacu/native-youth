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
namespace Celebros\ConversionPro\Controller\Plugin;

class OneColumnLayout
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
    
    public function afterExecute($controller, $result)
    {
        if ($result instanceof \Magento\Framework\View\Result\Page
            && $this->helper->isEnabledOnFrontend()
            && $this->helper->getNavToSearch())
        {
            $categoryId = $this->context->getRequest()->getParam('id');
            if (!$this->helper->isCategoryIdBlacklisted($categoryId))
                $result->getConfig()->setPageLayout('1column');
        }
        return $result;
    }
    
}