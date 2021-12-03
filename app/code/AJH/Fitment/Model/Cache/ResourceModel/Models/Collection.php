<?php

namespace AJH\Fitment\Model\Cache\ResourceModel\Models;

use AJH\Fitment\Model\Cache\ResourceModel\Models as ResourceModel;
use AJH\Fitment\Model\Cache\Models as Model;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {

    protected $_idFieldName = 'id';
    protected $_eventPrefix = 'cache_fitment_models_collection';
    protected $_eventObject = 'models_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct() {
        $this->_init(Model::class, ResourceModel::class);
    }

}
