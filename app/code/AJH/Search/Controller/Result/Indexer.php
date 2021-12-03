<?php

namespace AJH\Search\Controller\Result;

use \Magento\Framework\App\Action\Context;

class Indexer extends \Magento\Framework\App\Action\Action {    

    public function __construct(Context $context) {        
        parent::__construct($context);
    }

    public function execute() {
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }

}
