<?php

namespace AJH\D2R\Block\Retailer\Distributor;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use AJH\D2R\Helper\Distributor;

use Magento\Framework\App\Config\ScopeConfigInterface;

use AJH\D2R\Model\Source\Distributor as SourceDistributor;

class Search extends Template {

    protected $_distributor, $_scopeConfig, $_sourceDistributor;

    public function __construct(Context $context, Distributor $distributor, ScopeConfigInterface $scopeConfig, SourceDistributor $sourceDistributor) {
        $this->_distributor = $distributor;
        $this->_scopeConfig = $scopeConfig;    
        
        $this->_sourceDistributor = $sourceDistributor;

        parent::__construct($context);
    }

    protected function _construct() {        
        $this->setTemplate('AJH_D2R::retailer/distributor/search.phtml');
    }

    public function getDistributors() {
        return $this->_distributor->getDistributors();
    }
    
    public function getGMapsAPIKey() {
        $apiKey = $this->_scopeConfig->getValue('googleAPI/maps/api_key', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $apiKey;
    }
    
    public function getDistributorSource(){
        return $this->_sourceDistributor->toShortOptionArray();
    }

}
