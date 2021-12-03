<?php

namespace AJH\D2R\Model;

use Magento\Framework\Model\AbstractModel;
use AJH\D2R\Model\ResourceModel\Retailer as ResourceModel;

class Retailer extends AbstractModel {

    protected $_eventPrefix = 'd2r_retailer';

    public function _construct() {
        parent::_construct();
        $this->_init(ResourceModel::class);
    }

}
