<?php

namespace AJH\D2R\Block\Tpms;

use Magento\Framework\View\Element\Template\Context;
use AJH\D2R\Helper\Retailer;
use Magento\Customer\Model\Session;
use AJH\D2R\Helper\Address;
use AJH\D2R\Helper\Distributor;
use Inchoo\Search\Helper\Vehicle;

class Form extends \Magento\Framework\View\Element\Template {

    protected $_retailers, $_customerSession, $_address, $_distributor, $_vehicle;

    public function __construct(Context $context, Retailer $retailers, Session $customerSession, Address $address, Distributor $distributor, Vehicle $vehicle) {
        parent::__construct($context, array());
        $this->_retailers = $retailers;
        $this->_customerSession = $customerSession;
        $this->_address = $address;
        $this->_distributor = $distributor;
        $this->_vehicle = $vehicle;
    }

    public function isActiveRetailer() {
        return $this->_retailers->isActiveRetailer();
    }

    public function getCurrentCustomer() {
        return $this->_customerSession;
    }
    
    public function getAddress(){
        return $this->_address;
    }
    
    public function getDistributor(){
        return $this->_distributor;
    }
    
    public function getVehicle(){
        return $this->_vehicle;
    }

}
