<?php

namespace AJH\D2R\Controller\Retailer;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class Home extends Template {

    protected $_pageConfig;

    public function __construct(
    \Magento\Framework\View\Page\Config $pageConfig
    ) {
        $this->_pageConfig = $pageConfig;
    }

    public function execute() {
        echo 'd2r home';
        exit;
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }

}