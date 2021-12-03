<?php

namespace AJH\ProductVehicle\Model;

use Magento\Framework\Model\AbstractModel;
use AJH\ProductVehicle\Model\ResourceModel\Vehiclestpms as ResourceModel;

class Vehiclestpms extends AbstractModel {

    public function _construct() {
        $this->_init(ResourceModel::class);
    }
    
    protected function getVehicles() {
        
    }

}
