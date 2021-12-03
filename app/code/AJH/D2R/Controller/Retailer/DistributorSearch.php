<?php

namespace AJH\D2R\Controller\Retailer;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;

class DistributorSearch extends Action {

    protected $_pageConfig, $_customerSession;

    public function __construct(Context $context,
            \Magento\Framework\View\Page\Config $pageConfig,
            \Magento\Customer\Model\Session $customerSession
    ) {
        $this->_pageConfig = $pageConfig;
        $this->_customerSession = $customerSession;
        parent::__construct($context);
    }

    public function execute() {        
//        $this->_customerSession->authenticate($this);
        $this->_view->loadLayout()->renderLayout();
    }

}
