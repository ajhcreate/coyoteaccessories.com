<?php

namespace AJH\PageWarmer\Model\ResourceModel\Years;

use AJH\PageWarmer\Model\ResourceModel\Years as ResourceModel;
use AJH\PageWarmer\Model\Years as Model;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {

    protected $_idFieldName = 'id';
    protected $_eventPrefix = 'ajh_pagewarmer_years_collection';
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
