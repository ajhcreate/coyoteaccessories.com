<?php

namespace AJH\D2R\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Distributor extends AbstractDb {

    public function __construct(\Magento\Framework\Model\ResourceModel\Db\Context $context, $connectionName = "revo") {
        parent::__construct($context, $connectionName);
    }

    protected function _construct() {
        $this->_init('distributors', 'ID');
    }

}
