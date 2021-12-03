<?php

namespace AJH\ProductVehicle\Model;

use Magento\Framework\Model\AbstractModel;
use AJH\ProductVehicle\Model\ResourceModel\Partsubclass as ResourceModel;

class Partsubclass extends AbstractModel {

    public function _construct() {
        $this->_init(ResourceModel::class);
    }

}
