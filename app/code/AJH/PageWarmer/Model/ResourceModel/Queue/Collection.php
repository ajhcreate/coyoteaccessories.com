<?php

namespace AJH\PageWarmer\Model\ResourceModel\Queue;

use AJH\PageWarmer\Model\ResourceModel\Queue as ResourceModel;
use AJH\PageWarmer\Model\Queue as Model;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {

    protected $_idFieldName = 'id';
    protected $_eventPrefix = 'ajh_pagewarmer_queue_collection';
    protected $_eventObject = 'queue_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct() {
        $this->_init(Model::class, ResourceModel::class);
    }

}
