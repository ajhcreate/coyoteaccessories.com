<?php

namespace AJH\PageWarmer\Model\ResourceModel\SubModels;

use AJH\PageWarmer\Model\ResourceModel\SubModels as ResourceModel;
use AJH\PageWarmer\Model\SubModels as Model;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {

    protected $_idFieldName = 'id';
    protected $_eventPrefix = 'ajh_pagewarmer_submodels_collection';
    protected $_eventObject = 'submodels_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct() {
        $this->_init(Model::class, ResourceModel::class);
    }

}
