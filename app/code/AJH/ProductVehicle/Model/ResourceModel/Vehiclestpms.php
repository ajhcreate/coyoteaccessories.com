<?php

namespace AJH\ProductVehicle\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Vehiclestpms extends AbstractDb {

    public function __construct(\Magento\Framework\Model\ResourceModel\Db\Context $context, $connectionName = "revo") {
        parent::__construct($context, $connectionName);
    }

    public function _construct() {
        $this->_init('vwvehiclestpms4', 'Vehicleid');
        $this->_isPkAutoIncrement = false;
    }

}
