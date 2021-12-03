<?php

namespace AJH\D2R\Block\Retailer;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use AJH\D2R\Helper\Retailer;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Search extends Template {

    protected $_retailerHelper, $_scopeConfig;

    public function __construct(Context $context, Retailer $retailer, ScopeConfigInterface $scopeConfig) {
        parent::__construct($context, array());

        $this->_retailerHelper = $retailer;
        $this->_scopeConfig = $scopeConfig;
    }

    public function getRetailers() {
        return $this->_retailerHelper->getRetailers();
    }

    public function getGMapsAPIKey() {
        $apiKey = $this->_scopeConfig->getValue('googleAPI/maps/api_key', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $apiKey;
    }

}
