<?php

namespace AJH\FitmentUrlRewrite\Model\ResourceModel\SubModels;

use AJH\FitmentUrlRewrite\Model\ResourceModel\SubModels as ResourceModel;
use AJH\FitmentUrlRewrite\Model\SubModels as Model;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {

    protected $_idFieldName = 'id';
    protected $_eventPrefix = 'ajh_fitment_submodels_collection';
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
