<?php

namespace AJH\FitmentUrlRewrite\Model;

use Magento\Framework\Model\Context;
use Magento\Framework\Registry;        

class Makes extends \Magento\Framework\Model\AbstractModel {

    protected $_fitmentMakesCollection;

    public function __construct(
            Context $context,
            Registry $registry
    ) {
        parent::__construct($context, $registry);                
    }
    protected function _construct() {
        $this->_init('AJH\FitmentUrlRewrite\Model\ResourceModel\Makes');
    }

}
