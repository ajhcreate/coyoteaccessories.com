<?php

namespace AJH\ProductVehicle\Model\ResourceModel\Partsubclass;

use AJH\ProductVehicle\Model\ResourceModel\Partsubclass as ResourceModel;
use AJH\ProductVehicle\Model\Partsubclass as Model;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {

    public function _construct() {
        $this->_init(Model::class, ResourceModel::class);
    }

}
