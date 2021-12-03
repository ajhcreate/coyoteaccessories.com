<?php

namespace AJH\ProductVehicle\Model\ResourceModel\Partclass;

use AJH\ProductVehicle\Model\ResourceModel\Partclass as ResourceModel;
use AJH\ProductVehicle\Model\Partclass as Model;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {

    public function _construct() {
        $this->_init(Model::class, ResourceModel::class);
    }

    public function getPartClassByMagentoPartClassId($magentoPartClassId) {
        $this->addFieldToFilter('magento_id', array('eq' => $magentoPartClassId));
        return $this->getFirstItem();
    }

}
