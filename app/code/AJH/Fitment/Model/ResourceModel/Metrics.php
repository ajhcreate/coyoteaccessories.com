<?php

namespace AJH\Fitment\Model\ResourceModel;

class Metrics extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {

    public function __construct(\Magento\Framework\Model\ResourceModel\Db\Context $context, $connectionName = "revo") {
        parent::__construct($context, $connectionName);
    }

    public function _construct() {
        $this->_init('fitmentmetrics', 'fmt_year');
        $this->_isPkAutoIncrement = false;
    }
}
