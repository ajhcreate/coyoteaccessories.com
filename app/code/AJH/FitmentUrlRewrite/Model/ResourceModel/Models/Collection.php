<?php

namespace AJH\FitmentUrlRewrite\Model\ResourceModel\Models;

use AJH\FitmentUrlRewrite\Model\ResourceModel\Models as ResourceModel;
use AJH\FitmentUrlRewrite\Model\Models as Model;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {

    protected $_idFieldName = 'id';
    protected $_eventPrefix = 'ajh_fitment_models_collection';
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
