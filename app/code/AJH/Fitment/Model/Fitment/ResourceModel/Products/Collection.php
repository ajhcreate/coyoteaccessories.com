<?php

namespace AJH\Fitment\Model\Fitment\ResourceModel\Products;

use AJH\Fitment\Model\Fitment\Products as Model;
use AJH\Fitment\Model\Fitment\ResourceModel\Products as ResourceModel;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {

    protected $_idFieldName = 'id';
    protected $_eventPrefix = 'ajh_fitment_fitment_products_collection';
    protected $_eventObject = 'product_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct() {
        $this->_init(Model::class, ResourceModel::class);
    }

}
