<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_CustomerAttributes
 */
namespace Amasty\CustomerAttributes\Plugin\Customer\Metadata\FormElement;

class File
{
    public function aroundExtractValue($subject, \Closure $proceed, $request){
        $value = $proceed($request);
        $attrCode = $subject->getAttribute()->getAttributeCode();
        $files  = $request->getFiles();
        if ((!$value || !array_key_exists('name', $value) || !$value['name'])
            && !isset($files[$attrCode])
            && (!$value || !array_key_exists('delete', $value) ||  !$value['delete'])
        ) {
            $value = $request->getParam($attrCode, false);
        }

        return $value;
    }
}
