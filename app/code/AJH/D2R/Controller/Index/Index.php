<?php

namespace AJH\D2R\Controller\Index;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class Index extends \Magento\Framework\App\Action\Action {

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
    \Magento\Framework\App\Action\Context $context, \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory) {
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    /**
     * View  page action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute() {
        echo 'AJH\D2R\Controller\Index';
        exit;
//        $result = $this->resultJsonFactory->create();
//        $data = ['message' => 'Hello world!'];

        $this->_view->loadLayout();
        $this->_view->renderLayout();

//        return $result->setData($data);
    }

}
