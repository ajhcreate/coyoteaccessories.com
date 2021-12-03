<?php

namespace AJH\D2R\Model\ResourceModel\Help;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use AJH\D2R\Model\ResourceModel\Help as ResourceModel;
use AJH\D2R\Model\Help as Model;

class Collection extends AbstractCollection {

    /**
     * Define model & resource model
     */
    protected function _construct() {
        $this->_init(Model::class, ResourceModel::class);
    }

}
