<?php

namespace AJH\D2R\Block\Retailer\Distributor;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use AJH\D2R\Helper\Distributor as DistributorHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Search extends Template {

    protected $_distributorHelper;
    protected $_scopeConfig;

    public function __construct(Context $context, DistributorHelper $distributorHelper, ScopeConfigInterface $scopeConfig) {
        parent::__construct($context, array());
        $this->_distributorHelper = $distributorHelper;
        $this->_scopeConfig = $scopeConfig;
    }

    protected function _construct() {
        $this->setTemplate('AJH_D2R::retailer/distributor/search.phtml');
    }

    public function getDistributors() {
        return $this->_distributorHelper->getDistributors();
    }

    public function getAPIKey() {
        return $this->_scopeConfig('googleAPI/maps/api_key');
    }

}
