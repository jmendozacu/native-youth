<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Smartwave\Megamenu\Helper;

class Category extends \Magento\Catalog\Helper\Category
{
    
    public function getStoreCategories($sorted = false, $asCollection = false, $toLoad = true)
    {
        $parent = $this->_storeManager->getStore()->getRootCategoryId();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); 
        $categorymen = 46;
        $categorywoman = 47;

         $category = $this->_categoryFactory->create();

           $cat =  $category->load($categorymen); 
           $subcatarr = $cat->getAllChildren(true); 
           $catwoman =  $category->load($categorywoman); 
           $subcatarrwoman = $catwoman->getAllChildren(true);
           //print_r($subcatarrwoman);
           //die;
           //print_r($subcatarr); die;

            $om = \Magento\Framework\App\ObjectManager::getInstance(); 
            $session = $om->get('Magento\Catalog\Model\Session');
            $currentpcat=$session->getMenucategory();
   $categorys = $objectManager->get('Magento\Framework\Registry')->registry('current_category'); 
   if($categorys){  
            if(in_array($categorys->getId(), $subcatarr)) {
            $parent=46;
            }
            elseif(in_array($categorys->getId(), $subcatarrwoman))
            {
            $parent=47;
            }

        }
        elseif($currentpcat){
            //print_r($currentpcat[0]);
            //print_r($subcatarr);
          if(in_array(46, $currentpcat)) {
            $parent=46;
            }
            elseif(in_array(47, $currentpcat))
            {
                //die('test11');
            $parent=47;
            }
        }
             else{
                //die('testaa');
                $parent=47; 
            }


        $cacheKey = sprintf('%d-%d-%d-%d', $parent, $sorted, $asCollection, $toLoad);
        if (isset($this->_storeCategories[$cacheKey])) {
            return $this->_storeCategories[$cacheKey];
        }

        /**
         * Check if parent node of the store still exists
         */
        $category = $this->_categoryFactory->create();
        /* @var $category ModelCategory */
        if (!$category->checkId($parent)) {
            if ($asCollection) {
                return $this->_dataCollectionFactory->create();
            }
            return [];
        }

        $recursionLevel = max(
            0,
            (int)$this->scopeConfig->getValue(
                'catalog/navigation/max_depth',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            )
        );
        $storeCategories = $category->getCategories($parent, $recursionLevel, $sorted, $asCollection, $toLoad);

        $this->_storeCategories[$cacheKey] = $storeCategories;
        return $storeCategories;
    }

    
}
