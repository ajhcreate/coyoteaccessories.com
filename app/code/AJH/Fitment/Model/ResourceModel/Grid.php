<?php

namespace AJH\Fitment\Model\ResourceModel;

class Grid extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {

    public function __construct(\Magento\Framework\Model\ResourceModel\Db\Context $context,
            $connectionName = "revo") {
        parent::__construct($context, $connectionName);
    }

    protected function _construct() {
        $this->_init('vehicleparts', 'Vehicleid');
    }

}
