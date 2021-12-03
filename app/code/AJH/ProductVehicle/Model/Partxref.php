<?php
namespace AJH\ProductVehicle\Model;

use Magento\Framework\Model\AbstractModel;
use AJH\ProductVehicle\Model\ResourceModel\Partxref as ResourceModel;

class Partxref extends AbstractModel{
    
    public function _construct(){
        $this->_init(ResourceModel::class);
    }   
}