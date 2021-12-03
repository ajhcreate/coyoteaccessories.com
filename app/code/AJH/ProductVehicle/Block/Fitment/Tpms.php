<?php

namespace AJH\ProductVehicle\Block\Fitment;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use AJH\ProductVehicle\Model\VehiclestpmsFactory as VehiclestpmsCollection;
use AJH\ProductVehicle\Model\VehiclepartsFactory as VehiclepartsCollection;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\ProductFactory;
use Magento\Store\Model\StoreManagerInterface;

class Tpms extends Template {

    protected $_vehiclestpmsCollection, $_product, $_productloader, $_vehiclepartsCollection, $_storeManager, $_registry;

    public function __construct(Context $context, Registry $registry,
            VehiclestpmsCollection $vehiclestpmsCollection,
            ProductRepositoryInterface $productRepository,
            ProductFactory $productloader,
            VehiclepartsCollection $vehiclepartsCollection,
            StoreManagerInterface $storeManager) {
        parent::__construct($context);
        $this->_vehiclestpmsCollection = $vehiclestpmsCollection;
        $this->_vehiclepartsCollection = $vehiclepartsCollection;

        $this->_productRepository = $productRepository;
        $this->_registry = $registry;

        $this->_storeManager = $storeManager;

        $this->_productloader = $productloader;
    }

    protected function _construct() {
        $this->setTemplate('AJH_ProductVehicle::catalog/product/view/tpms.phtml');
    }

    protected function _prepareLayout() {
        parent::_prepareLayout();

        $store_id = $this->getStoreId();

        if (intval($store_id) === 1) {
            $pager = $this->getLayout()->createBlock(
                            'Magento\Theme\Block\Html\Pager', 'fitment.vehicle.pager2'
                    )->setAvailableLimit(array(20 => 20))->setShowPerPage(true)->setCollection(
                    $this->getCAProductVehicles()
            );
        } else {
            $pager = $this->getLayout()->createBlock(
                            'Magento\Theme\Block\Html\Pager', 'fitment.vehicle.pager2'
                    )->setAvailableLimit(array(20 => 20))->setShowPerPage(true)->setCollection(
                    $this->getProductVehicles()
            );
        }

        $pager->setData('productid', $this->getProduct()->getId());
        $pager->setTemplate('AJH_ProductVehicle::page/html/vehiclepager.phtml');

        $this->setChild('pager', $pager);

        if (intval($store_id) === 1) {
            $this->getCAProductVehicles()->load();
        } else {
            $this->getProductVehicles()->load();
        }

        return $this;
    }

    public function getStoreId() {
        return $this->_storeManager->getStore()->getId();
    }

    public function getProductVehiclesCount() {
        $sku = $this->getProductSku();
        $productVehiclesCount = $this->_vehiclestpmsCollection->create()
                        ->getVehicleInfoByPartNumberWithProtocol($sku)->getSize();
        return $productVehiclesCount;
    }

    public function getWAProductVehiclesCount() {
        $sku = $this->getProductSku();
        $productVehiclesCount = $this->_vehiclestpmsCollection->create()
                        ->getVehicleInfoByPartNumberWithProtocol($sku)->getSize();
        return $productVehiclesCount;
    }

    public function getProductId() {
        return $this->getRequest()->getParam('pid');
    }

    public function getVehiclesTpmsCollection() {
        $collection = $this->_vehiclestpmsCollection->create()->getCollection();
//
        $_vehicles = $collection->getVehicles();

        return $_vehicles;
    }

    /**
     * Retrieve current product
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct() {
        if (is_null($this->_product)) {
            $this->_product = $this->_registry->registry('product');

            if (is_null($this->_product)) {
                $this->_product = $this->_productloader->create()->load($this->getProductId());
            }

            if (!$this->_product->getId()) {
                throw new LocalizedException(__('Failed to initialize product'));
            }
        }

        return $this->_product;
    }

    public function getProductSku() {
        if ($this->getProduct()) {
            return $this->getProduct()->getSku();
        } elseif ($productSku = $this->getRequest()->getParam('productsku')) {
            return $productSku;
        }
    }

    public function isUniversalSensor() {
        $_is_universal = $this->getProduct()->getData('is_universal_sensor');

//        if (Mage::app()->getRequest()->getParam('productsku', null)) {
//            $productSku = Mage::app()->getRequest()->getParam('productsku', null);
//            $_product = Mage::getModel('catalog/product')->loadByAttribute('sku', $productSku);
//
//            $_is_universal = $_product->getData('is_universal_sensor');
//        }

        return $_is_universal;
    }

    public function getProductVehicles() {


        //get values of current page
        $page = ($this->getRequest()->getParam('p')) ? $this->getRequest()->getParam('p') : 1;
        //get values of current limit
        $pageSize = ($this->getRequest()->getParam('limit')) ? $this->getRequest()->getParam('limit') : 20;

        $sku = $this->getProductSku();
        $params = $this->getRequest()->getParams();

        $yearID = $this->getParamValue('year');
        $makeID = $this->getParamValue('make');
        $modelID = $this->getParamValue('model');
        $submodelID = $this->getParamValue('submodel');

        $collection = $this->_vehiclestpmsCollection->create()->getCollection();

        if ($yearID > 0) {
            $collection->addFieldToFilter('YearID', ['eq' => $yearID]);
        }
        
        /* addFieldToFilter is in the Collection.php using $params */

//        if ($makeID > 0) {
//            $collection->addFieldToFilter('MakeID', ['eq' => $makeID]);
//        }

//        if ($modelID > 0) {
//            $collection->addFieldToFilter('ModelID', ['eq' => $modelID]);
//        }
//
//        if ($submodelID > 0) {
//            $collection->addFieldToFilter('SubModelID', ['eq' => $submodelID]);
//        }

        $collection->setPageSize($pageSize);
        $collection->setCurPage($page);
        $productVehicles = $collection->getVehicleInfoByPartNumberWithProtocol($sku, $params);

        return $productVehicles;
    }

    private function getParamValue($param) {
        $params = $this->getRequest()->getParams();

        return isset($params[$param]) ? intval($params[$param]) : 0;
    }

    public function getPagerHtml() {
        return $this->getChildHtml('pager');
    }

    public function getPDQ() {
        $vehicles = $this->getProductVehicles();
        $pdq = [];

        foreach ($vehicles as $vehicle) {
            $pdq[$vehicle->getData('partnumber')] = array(
                'pdq' => $vehicle->getData('LinkedDate_PDQ'),
                'ateq' => $vehicle->getData('LinkedDate_ATEQ'),
                'bartec' => $vehicle->getData('LinkedDate_Bartec')
            );
        }

        return $pdq;
    }

}
