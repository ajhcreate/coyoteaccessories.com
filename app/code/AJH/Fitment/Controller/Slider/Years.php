<?php

namespace AJH\Fitment\Controller\Slider;

use Magento\Framework\App\Action\Action as ControllerAction;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;

class Years extends ControllerAction {

    protected $_resultJsonFactory;

    public function __construct(Context $context, JsonFactory $resultJsonFactory) {
        parent::__construct($context);

        $this->_resultJsonFactory = $resultJsonFactory;
    }

    public function execute() {
        $result = $this->_resultJsonFactory->create();
        $isAjax = $this->getRequest()->isAjax();
        if ($isAjax) {
        $output = $this->_view->getLayout()->createBlock('AJH\Fitment\Block\Widget\Slider')->setTemplate('AJH_Fitment::widget/slider/years.phtml')->toHtml();
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(json_encode(['outputHtml' => $output]));

//            return $result->setData($output);
        }
    }

}
