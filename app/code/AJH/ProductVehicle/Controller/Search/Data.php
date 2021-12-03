<?php

namespace AJH\ProductVehicle\Controller\Search;

use Magento\Framework\App\Config\ScopeConfigInterface;

class Data extends \Magento\Framework\App\Action\Action {

    protected $_scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig) {        
        $this->_scopeConfig = $scopeConfig;        
    }

    public function execute() {
        $this->loadLayout();
        
        $this->renderLayout();
    }

}
