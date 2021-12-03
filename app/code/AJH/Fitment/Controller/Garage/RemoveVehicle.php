<?php

namespace AJH\Fitment\Controller\Garage;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use AJH\Fitment\Model\Fitment\Garage;

class RemoveVehicle extends \Magento\Framework\App\Action\Action {

    protected $_resultJsonFactory;
    private $_garage;

    public function __construct(Context $context,
            JsonFactory $resultJsonFactory, Garage $garage) {
        parent::__construct($context);

        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_garage = $garage;
    }

    public function execute() {
        $isAjax = $this->getRequest()->isAjax();        
        
        if ($isAjax) {
            $response = $this->_garage->removeVehicleFromGarage();
            $output = $response;

            $this->getResponse()->setHeader('Content-type', 'application/json');
            $this->getResponse()->setBody(json_encode(['outputHtml' => $output]));
        }
    }

}
