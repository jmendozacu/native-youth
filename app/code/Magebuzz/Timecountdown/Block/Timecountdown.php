<?php
/**
 * @copyright Copyright (c) 2016 www.magebuzz.com
 */

namespace Magebuzz\Timecountdown\Block;

/**
 * Product View block
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Timecountdown extends \Magento\Framework\View\Element\Template
{

    protected $_product = null;
    protected $_category = null;

    protected $_coreRegistry = null;

    protected $_date;
    public $_scopeConfig;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        array $data = []
    )
    {
        
        $this->_coreRegistry = $registry;
        $this->_date = $date;
        $this->_scopeConfig = $context->getScopeConfig();
        parent::__construct($context, $data);
        
        $this->_product = $this->getProduct();
        $this->_category = $this->getCategory();
        
    }

    public function isPriceCountdown($product)
    { 
        $currentDate = date('d-m-Y');
        $currentTime = date('H:i:s');
        //var_dump($currentTime);
        //$currentTime = (explode(':', $currentTim, -1);
        // if ($product->getTypeId() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
        //         $_children = $product->getTypeInstance()->getUsedProducts($product);
        //         foreach ($_children as $child)
        //         {
        //         $todate = $child->getData('launch_date');
        //         //$fromdate = $child->getSpecialFromDate();
        //         var_dump($todate); //var_dump($fromdate);
        //             if ($child->getSpecialPrice() && $todate) 
        //             {
        //                 if(strtotime($todate) >= strtotime($currentDate) && strtotime($fromdate) <= strtotime($currentDate))
        //                 {
        //                 return true;
        //                 }
        //             }
        //                 //return false;
        //         }
        //         //var_dump($todate);
        // }
        // else{
        $currentTime = explode(":",$currentTime);
        //var_dump($currentTime[0]);
        $todate = $product->getData('launch_date'); 
        $totime = $product->getAttributeText('launch_time');//var_dump($totime);
        $available = $product->getData('availability_status'); 
        if ($available == '120' && $product->getIsShowCountdown() && $todate) {
            if (strtotime($todate) > strtotime($currentDate)) {
                return true;
            }
            elseif (strtotime($todate) == strtotime($currentDate)) {
                if($totime > $currentTime[0]){
                return true;
                }
            }
        }
        return false;
    }

    public function getProduct()
    {
        return $this->_coreRegistry->registry('product');
    }
    
    public function getCategory()
    {
        return $this->_coreRegistry->registry('current_category');
    }
    
    public function getPageType() {
        if ($this->_product && $this->_product->getId()) {
            return 'product';
        } else if ( $this->_category && $this->_category->getId()) {
            return 'category';
        } else if ($this->getScopeConfig('timecountdown/general/display_in_one')) {
            return 'homepage-one';
        }
        return 'homepage';
    }
    
    public function getScopeConfig($path) {
        return $this->_scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}
