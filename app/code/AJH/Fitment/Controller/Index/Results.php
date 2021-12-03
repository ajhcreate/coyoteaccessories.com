<?php

namespace AJH\Fitment\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use AJH\FitmentUrlRewrite\Model\UrlRewrite;

class Results extends \Magento\Framework\App\Action\Action {

    protected $_resultJsonFactory;
    protected $_urlRewrite;

    public function __construct(
    Context $context, JsonFactory $resultJsonFactory, UrlRewrite $urlRewrite) {

        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_urlRewrite = $urlRewrite;

        return parent::__construct($context);
    }

    public function execute() {
        $resultJson = $this->_resultJsonFactory->create();
        $year = $this->getRequest()->getParam('year');
        $makeID = $this->getRequest()->getParam('make');
        $modelID = $this->getRequest()->getParam('model');
        $submodelID = $this->getRequest()->getParam('submodel');
        $criteria_question = $this->getRequest()->getParam('criteria_q');
        $criteria = $this->getRequest()->getParam('criteria');
        $qualifiers = $this->getRequest()->getParam('qualifiers');
        $_qualifiers = $this->getRequest()->getParam('_qualifiers');

        $sef_url = $this->_urlRewrite->getFitmentSefUrl($year, $makeID, $modelID, $submodelID, $qualifiers, $_qualifiers) . "?criteria=" . $criteria;

        return $resultJson->setData([
                    "year" => $year,
                    "make" => $makeID,
                    "model" => $modelID,
                    "submodel" => $submodelID,
                    "criteria_q" => $criteria_question,
                    "criteria" => $criteria,
                    "url" => $sef_url
        ]);
    }
}
