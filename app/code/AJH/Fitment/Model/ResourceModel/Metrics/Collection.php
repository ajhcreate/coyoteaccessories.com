<?php

namespace AJH\Fitment\Model\ResourceModel\Metrics;

use AJH\Fitment\Model\ResourceModel\Metrics as ResourceModel;
use AJH\Fitment\Model\Metrics as Model;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {

    protected $_idFieldName = 'id';
    protected $_eventPrefix = 'ajh_fitment_metrics_collection';
    protected $_eventObject = 'fitment_metrics_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct() {
        $this->_init(Model::class, ResourceModel::class);
    }

}
