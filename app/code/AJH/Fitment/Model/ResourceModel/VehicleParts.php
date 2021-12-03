<?php

namespace AJH\Fitment\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\Context;

class VehicleParts extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {

    public function __construct(Context $context, $connectionName = "revo"){
        parent::__construct($context, $connectionName);
    }

    protected function _construct() {        
        $this->_init('vehicleparts', 'PartNumber');
    }

}
