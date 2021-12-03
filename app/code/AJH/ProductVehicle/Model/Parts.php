<?php

namespace AJH\ProductVehicle\Model\Parts;

use Magento\Framework\Model\AbstractModel;
use AJH\ProductVehicle\Model\ResourceModel\Parts as ResourceModel;

class Parts extends AbstractModel {

    public function _construct() {
        $this->_init(ResourceModel::class);
    }

}
