<?php

namespace AJH\Customerfields\Controller\Index;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class Index extends Template {

    protected $_pageConfig;

    public function __construct(
    \Magento\Framework\View\Page\Config $pageConfig
    ) {
        $this->_pageConfig = $pageConfig;
    }

    public function execute() {
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }

}
