<?php

namespace AJH\ProductVehicle\Model\ResourceModel\Models;

use AJH\ProductVehicle\Model\ResourceModel\Models as ResourceModel;
use AJH\ProductVehicle\Model\Models as Model;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {

    public function _construct() {
        $this->_init(Model::class, ResourceModel::class);
    }

}
