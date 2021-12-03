<?php

namespace AJH\D2R\Model\ResourceModel\Retailer;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use AJH\D2R\Model\ResourceModel\Retailer as ResourceModel;
use AJH\D2R\Model\Distributor as Model;

class Collection extends AbstractCollection {

    protected function _construct() {
        parent::_construct();
        $this->_init(Model::class, ResourceModel::class);
    }

}
