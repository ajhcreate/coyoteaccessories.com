<?php

namespace AJH\PageWarmer\Model\ResourceModel\Makes;

use AJH\PageWarmer\Model\ResourceModel\Makes as ResourceModel;
use AJH\PageWarmer\Model\Makes as Model;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {

    protected $_idFieldName = 'id';
    protected $_eventPrefix = 'ajh_pagewarmer_makes_collection';
    protected $_eventObject = 'makes_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct() {
        $this->_init(Model::class, ResourceModel::class);
    }

}
