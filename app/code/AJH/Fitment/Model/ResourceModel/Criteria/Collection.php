<?php

namespace AJH\Fitment\Model\ResourceModel\Criteria;

use AJH\Fitment\Model\ResourceModel\Criteria as ResourceModel;
use AJH\Fitment\Model\Criteria as Model;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {

    protected $_idFieldName = 'id';
    protected $_eventPrefix = 'ajh_fitment_criteria_collection';
    protected $_eventObject = 'criteria_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct() {
        $this->_init(Model::class, ResourceModel::class);
    }

}
