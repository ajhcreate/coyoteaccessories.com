<?php
namespace AJH\ProductVehicle\Model;

use Magento\Framework\Model\AbstractModel;
use AJH\ProductVehicle\Model\ResourceModel\Vehicle as ResourceModel;

class Vehicle extends AbstractModel{
    
    public function _construct(){
        $this->_init(ResourceModel::class);
    }   
}