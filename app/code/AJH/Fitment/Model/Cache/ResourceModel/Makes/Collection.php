<?php

namespace AJH\Fitment\Model\Cache\ResourceModel\Makes;

use AJH\Fitment\Model\Cache\ResourceModel\Makes as ResourceModel;
use AJH\Fitment\Model\Cache\Makes as Model;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {

    protected $_idFieldName = 'id';
    protected $_eventPrefix = 'cache_fitment_makes_collection';
    protected $_eventObject = 'cache_makes_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct() {
        $this->_init(Model::class, ResourceModel::class);
    }

}
