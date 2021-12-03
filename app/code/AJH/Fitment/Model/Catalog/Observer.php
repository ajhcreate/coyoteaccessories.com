<?php

namespace AJH\Fitment\Model\Catalog;

class Observer {

    private $_fitment_skus = array();

    public function __construct() {
        
    }

    public function catalog_product_list_collection($observer) {

        $skus = Mage::getSingleton('AJH_Fitment_Model_Fitment_Api')->loadFitmentSkus();

        foreach ($skus as $sku) {
            array_push($this->_fitment_skus, $sku);
        }

        if (count($this->_fitment_skus)) {
            $collection = $observer->getEvent()->getCollection();
            $collection->addAttributeToFilter('sku', array('in' => $this->_fitment_skus));
        }

        return $this;
    }

}
