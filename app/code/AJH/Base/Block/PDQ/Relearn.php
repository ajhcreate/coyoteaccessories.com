<?php

namespace AJH\Base\Block\PDQ;

use Magento\Framework\View\element\Template\Context;
use AJH\D2R\Helper\Retailer;
use Inchoo\Search\Helper\Vehicle;

class Relearn extends \Magento\Framework\View\Element\Template {

    protected $_retailer, $_vehicle;

    public function __construct(Context $context, Retailer $retailer, Vehicle $vehicle) {
        parent::__construct($context);

        $this->_retailer = $retailer;
        $this->_vehicle = $vehicle;
    }

    public function isActiveRetailer() {
        return $this->_retailer->isActiveRetailer();
    }

    public function getYears() {
        return $this->_vehicle->getYears();
    }

    public function getRelearnHtml() {
        $params = $this->getRequest()->getParams();

        $procId = isset($params['Id']) ? $params['Id'] : false;
        if (!$procId) {
            return;
        }

        return $this->_vehicle->getRelearnHtml($procId);
    }

    public function _getRequests() {
        $params = $this->getRequest()->getParams();
        $_params = array();

        foreach ($params as $key => $param) {
            $_params[$key] = $param;
        }

        return $_params;
    }

}
