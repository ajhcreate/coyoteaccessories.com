<?php

namespace AJH\Fitment\Model\ResourceModel\Years;

use AJH\Fitment\Model\ResourceModel\Years as ResourceModel;
use AJH\Fitment\Model\Years as Model;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {

    protected $_idFieldName = 'id';
    protected $_eventPrefix = 'ajh_fitment_years_collection';
    protected $_eventObject = 'years_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct() {
        $this->_init(Model::class, ResourceModel::class);
    }

}
