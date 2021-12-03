<?php

namespace AJH\Fitment\Model\ResourceModel\Qualifiers;

use AJH\Fitment\Model\ResourceModel\Qualifiers as ResourceModel;
use AJH\Fitment\Model\Qualifiers as Model;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {

    protected $_idFieldName = 'id';
    protected $_eventPrefix = 'ajh_fitment_qualifiers_collection';
    protected $_eventObject = 'qualifiers_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct() {
        $this->_init(Model::class, ResourceModel::class);
    }

}
