<?php

namespace AJH\ProductVehicle\Model;

use Magento\Framework\Model\AbstractModel;
use AJH\ProductVehicle\Model\ResourceModel\Partclass as ResourceModel;

class Partclass extends AbstractModel {

    public function _construct() {
        $this->_init(ResourceModel::class);
    }

}
