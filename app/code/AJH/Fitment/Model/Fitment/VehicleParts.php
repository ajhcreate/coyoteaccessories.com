<?php

namespace AJH\Fitment\Model\Fitment;

use Magento\Framework\Model\Context;
use Magento\Framework\Registry;

class VehicleParts extends \Magento\Framework\Model\AbstractModel {

    public function __construct(Context $context, Registry $registry) {

        parent::__construct($context, $registry);
    }

    protected function _construct() {
        $this->_init('AJH\Fitment\Model\Fitment\ResourceModel\VehicleParts');
    }

}
