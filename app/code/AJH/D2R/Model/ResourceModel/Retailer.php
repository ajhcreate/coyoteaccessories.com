<?php

namespace AJH\D2R\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Retailer extends AbstractDb {

    public function _construct() {
        $this->_init('retailers', 'ID');
    }

}
