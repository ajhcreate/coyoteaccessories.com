<?php

namespace AJH\PageWarmer\Model\ResourceModel\Status;

use AJH\PageWarmer\Model\ResourceModel\Makes as ResourceModel;
use AJH\PageWarmer\Model\Status as Status;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {

    protected $_idFieldName = 'id';
    protected $_eventPrefix = 'ajh_pagewarmer_status_collection';
    protected $_eventObject = 'status_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct() {
        $this->_init(Status::class, ResourceModel::class);
    }

}
