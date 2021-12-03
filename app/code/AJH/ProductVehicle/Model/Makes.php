<?php

namespace AJH\ProductVehicle\Model;

use Magento\Framework\Model\AbstractModel;
use AJH\ProductVehicle\Model\ResourceModel\Makes as ResourceModel;

class Makes extends AbstractModel {

    public function _construct() {
        $this->_init(ResourceModel::class);
    }

}
