<?php

namespace AJH\Fitment\Model\ResourceModel;

class Criteria extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {

    public function __construct(\Magento\Framework\Model\ResourceModel\Db\Context $context, $connectionName = "revo") {
        parent::__construct($context, $connectionName);
    }

    public function _construct() {
        $this->_init('vwvehiclestpms4', null);
        $this->_isPkAutoIncrement = false;
    }
}
