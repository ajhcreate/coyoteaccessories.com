<?php

namespace AJH\ProductVehicle\Block\Fitment;

use Magento\Framework\View\Element\Template;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template\Context;
use AJH\ProductVehicle\Block\Product\View\FitmentList;
use Magento\Catalog\Api\ProductRepositoryInterface;
use AJH\ProductVehicle\Model\VehiclepartsFactory as VehiclepartsCollection;

class Items extends Template {

    protected $_helperData, $_vehiclepartsCollection;
    protected $_urlInterface, $_fitmentList, $_productloader, $_product;
    protected $_productRepository;

    public function __construct(Context $context, UrlInterface $urlInterface,
            FitmentList $fitmentList,
            ProductRepositoryInterface $productRepository,
            VehiclepartsCollection $vehiclepartsCollection) {
        parent::__construct($context);

        $this->_fitmentList = $fitmentList;
        $this->_urlInterface = $urlInterface;

        $this->_productRepository = $productRepository;

        $this->_vehiclepartsCollection = $vehiclepartsCollection;
    }

    public function _construct() {
        $this->setTemplate('AJH_ProductVehicle::fitment/items.phtml');
    }

    public function getItemsCollection() {
        $sku = $this->getProductSku();
        $year = $this->getRequest()->getParam('year');
        $make = $this->getRequest()->getParam('make') ? $this->getRequest()->getParam('make') : null;
        $model = $this->getRequest()->getParam('model') ? $this->getRequest()->getParam('model') : null;
        $submodel = $this->getRequest()->getParam('submodel') ? $this->getRequest()->getParam('submodel') : null;

        $collection = $this->_vehiclepartsCollection->create()->getCollection();
        $collection->addFieldToSelect("PartNumber");
        $productVehicles = $collection->getCAVehicles($sku);
        if (!is_null($year)) {
            $productVehicles->setFilter('YearID', $year);
        }                
        if (!is_null($make)) {
            $productVehicles->setFilter('MakeID', $make);
        }
        if (!is_null($model)) {
            $productVehicles->setFilter('ModelID', $model);
        }
        if (!is_null($submodel)) {
            $productVehicles->setFilter('SubModelID', $submodel);
        }

        return $productVehicles;
    }

    public function getYears() {
        $collection = $this->getItemsCollection();

        $items = [];
        $items['label'] = "Year";
        foreach ($collection as $item) {
            $items['items'][$item->getData('YearID')] = $item->getData('YearID');
        }        

        return $items;
    }
    
    public function getMakes() {
        $collection = $this->getItemsCollection();

        $items = [];
        $items['label'] = "Make";
        foreach ($collection as $item) {
            $items['items'][$item->getData('MakeID')] = $item->getData('MakeName');
        }        

        return $items;
    }
    
    public function getModels() {        
        $collection = $this->getItemsCollection();

        $items = [];
        $items['label'] = "Model";
        foreach ($collection as $item) {            
                $items['items'][$item->getData('ModelID')] = $item->getData('ModelName');            
        }       

        return $items;
    }
    
    public function getSubModels() {        
        $collection = $this->getItemsCollection();

        $items = [];
        $items['label'] = "Sub Model";
        foreach ($collection as $item) {            
                $items['items'][$item->getData('SubModelID')] = $item->getData('SubModelName');            
        }       

        return $items;
    }

    public function getProductSku() {
        if ($this->getProduct()) {
            return $this->getProduct()->getSku();
        }

        return;
    }

    public function getProduct() {
        $pid = $this->getRequest()->getParam('pid');

        $this->_product = $this->getProductById($pid);

        if (!$this->_product->getId()) {
            throw new LocalizedException(__('Failed to initialize product'));
        }

        return $this->_product;
    }

    public function getProductById($id) {
        return $this->_productRepository->getById($id);
    }

    public function getProductBySku($sku) {
        return $this->_productRepository->get($sku);
    }

    public function getFitmentData() {
        $fitment = $this->getRequest()->getParam('fitment');

        switch ($fitment) {
            case 'submodels':
                return $this->getSubModels();
            case 'models':
                return $this->getModels();
            case 'makes':
                return $this->getMakes();                
            default:
                return $this->getYears();                
        }
    }
}
