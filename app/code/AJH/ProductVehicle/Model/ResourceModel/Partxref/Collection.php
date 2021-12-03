<?php

namespace AJH\ProductVehicle\Model\ResourceModel\Partxref;

use AJH\ProductVehicle\Model\ResourceModel\Partxref as ResourceModel;
use AJH\ProductVehicle\Model\Partxref as Model;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {

    public function _construct() {
        $this->_init(Model::class, ResourceModel::class);
    }

}
