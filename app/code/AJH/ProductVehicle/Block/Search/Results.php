<?php

namespace AJH\ProductVehicle\Block\Search;

use Magento\Framework\View\Element\Template;
use AJH\ProductVehicle\Helper\Data as ProductVehicleHelperData;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template\Context;
use AJH\ProductVehicle\Model\ResourceModel\Vehiclestpms\Collection as VehiclestpmsCollection;

class Results extends Template{
    
    protected $_helperData, $_vehiclestpmsCollection;
    protected $_urlInterface;

    public function __construct(Context $context, ProductVehicleHelperData $helperData, UrlInterface $urlInterface, VehiclestpmsCollection $vehiclestpmsCollection){
        parent::__construct($context);
        
        $this->_helperData = $helperData;
        $this->_vehiclestpmsCollection = $vehiclestpmsCollection;
        
        $this->_urlInterface = $urlInterface;     
    }
    
//    protected function _prepareLayout()
//    {
//        parent::_prepareLayout();
//        if ($breadcrumbs = $this->getLayout()->getBlock('breadcrumbs')) {
//            $breadcrumbs->addCrumb('home', array(
//                'label' => __('Home'),
//                'title' => __('Go to Home Page'),
//                'link' => $this->_urlInterface->getBaseUrl()
//            ))->addCrumb('search', array(
//                'label' => __('Search by Parts'),
//                'link'=>$this->_urlInterface->getUrl('*')
//            ))->addCrumb('search_result', array(
//                'label' => __('Results')
//            ));
//        }
//        $title = $this->__("Search results for: '%s'", $this->getSearchQuery());
//        $this->getLayout()->getBlock('head')->setTitle($title);
//        
//        return $this;
//    }

    public function getProductVehicles()
    {
        $partnumber = $this->getSearchQuery();
        return $this->_vehiclestpmsCollection->getVehicleInfoByPartNumber($partnumber);
    }

    public function getSearchQuery()
    {
        return $this->_helperData->getEscapedQueryText();
    }

    public function getNoResultText()
    {
        return $this->_helperData->getNoResultText();
    }
}