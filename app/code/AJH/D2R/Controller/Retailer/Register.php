<?php

namespace AJH\D2R\Controller\Retailer;

use Magento\Framework\App\Action\Context;

class Register extends \Magento\Framework\App\Action\Action {

    protected $_pageConfig, $_customerSession, $_helper, $_retailerHelper, $_distributorHelper;
    protected $_retailerStatus, $_request;

    public function __construct(Context $context, \Magento\Framework\View\Page\Config $pageConfig) {
        parent::__construct($context);
        $this->_pageConfig = $pageConfig;
    }

    public function execute() {        
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }

}
