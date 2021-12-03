<?php

namespace AJH\Fitment\Plugin;

class Layer {

    public function aroundGetProductCollection(
    \Magento\Catalog\Model\Layer $subject, \Closure $proceed
    ) {                

        $result = $proceed();
//        $result->addAttributeToFilter('sku', array('in' => ['8170-8170N-E']));
//        
//        echo $result->getSelect()->__toString();
//        die;
        
        return $result;
    }

}
