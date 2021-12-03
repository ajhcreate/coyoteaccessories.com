<?php

namespace AJH\PageWarmer\Model;

use Magento\Framework\Model\Context;
use Magento\Framework\Registry;        
use AJH\PageWarmer\Model\MakesFactory as FitmentMakesCollection;

class Makes extends \Magento\Framework\Model\AbstractModel {

    protected $_fitmentMakesCollection;

    public function __construct(
            Context $context,
            Registry $registry,            
            FitmentMakesCollection $fitmentMakesCollection
    ) {
        parent::__construct($context, $registry);
        
        $this->_fitmentMakesCollection = $fitmentMakesCollection;

    }

    protected function _construct() {
        $this->_init('AJH\PageWarmer\Model\ResourceModel\Makes');
    }

}
