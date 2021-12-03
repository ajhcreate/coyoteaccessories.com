<?php

namespace AJH\PageWarmer\Model;

use Magento\Framework\Model\Context;
use Magento\Framework\Registry;

class Queue extends \Magento\Framework\Model\AbstractModel {

    public function __construct(
    Context $context, Registry $registry
    ) {
        parent::__construct($context, $registry);
    }
    
    protected function _construct() {
        $this->_init('AJH\PageWarmer\Model\ResourceModel\Queue');
    }

}
