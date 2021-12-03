<?php

namespace AJH\Fitment\Controller\Garage;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;

class Index extends \Magento\Framework\App\Action\Action {

    protected $_resultJsonFactory;

    public function __construct(Context $context, JsonFactory $resultJsonFactory) {
        parent::__construct($context);

        $this->_resultJsonFactory = $resultJsonFactory;
    }

    public function execute() {
        $isAjax = $this->getRequest()->isAjax();
//        if ($isAjax) {
            $output = $this->_view->getLayout()->createBlock('AJH\Fitment\Block\Garage')->setTemplate('AJH_Fitment::garage.phtml')->toHtml();

            $this->getResponse()->setHeader('Content-type', 'application/json');
            $this->getResponse()->setBody(json_encode(['outputHtml' => $output]));
//        }
    }

}
