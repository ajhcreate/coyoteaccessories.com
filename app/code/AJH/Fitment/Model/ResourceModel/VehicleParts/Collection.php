<?php

namespace AJH\Fitment\Model\ResourceModel\VehicleParts;

use AJH\Fitment\Model\ResourceModel\VehicleParts as ResourceModel;
use AJH\Fitment\Model\VehicleParts as Model;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {

    protected $_idFieldName = 'id';
    protected $_eventPrefix = 'ajh_fitment_revo_vehicleparts_collection';
    protected $_eventObject = 'revo_fitment_vehicleparts_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct() {        
        $this->_init(Model::class, ResourceModel::class);
    }

}
