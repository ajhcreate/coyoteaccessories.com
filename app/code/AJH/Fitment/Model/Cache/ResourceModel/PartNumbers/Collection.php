<?php

namespace AJH\Fitment\Model\Cache\ResourceModel\PartNumbers;

use AJH\Fitment\Model\Cache\ResourceModel\PartNumbers as ResourceModel;
use AJH\Fitment\Model\Cache\PartNumbers as Model;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {

    protected $_idFieldName = 'id';
    protected $_eventPrefix = 'ajh_fitment_partnumbers_collection';
    protected $_eventObject = 'partnumbers_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct() {
        $this->_init(Model::class, ResourceModel::class);
    }

}
