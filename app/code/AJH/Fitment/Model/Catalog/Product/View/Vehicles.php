<?php

namespace AJH\Fitment\Model\Catalog\Product\View;

class Vehicles extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {
    
    public function _construct(){}

    public function getPartFitmentVehicles() {
        $vehicles = Mage::getResourceModel('fitment/catalog_collection');
                
        return $vehicles->getVehicleInfoByPartNumber('NONE');
    }

}
