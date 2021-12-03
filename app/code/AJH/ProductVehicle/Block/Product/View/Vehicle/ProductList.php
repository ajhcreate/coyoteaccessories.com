<?php

namespace AJH\ProductVehicle\Block\Product\View\Vehicle;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use AJH\ProductVehicle\Model\ResourceModel\Vehiclestpms\Collection as VehiclestpmsCollection;
use Magento\Catalog\Api\ProductRepositoryInterface;

class ProductList extends Template {

    protected $_vehiclestpmsCollection;

    public function __construct(Context $context, Registry $registry, VehiclestpmsCollection $vehiclestpmsCollection, ProductRepositoryInterface $productRepository) {
        parent::__construct($context);
        $this->_vehiclestpmsCollection = $vehiclestpmsCollection;

        $this->_productRepository = $productRepository;
        $this->_registry = $registry;
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
        return $this->getRequest()->getParam('id');
    }

    /**
     * Retrieve current product
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct() {
        if (!$this->_registry->registry('product') && $this->getProductId()) {
            $product = $this->_productRepository->load($this->getProductId());
            $this->_registry->register('product', $product);
        }
        return $this->_registry->registry('product');
    }

    public function getProductSku() {
        if ($this->getProduct()) {
            return $this->getProduct()->getSku();
        } elseif ($productSku = $this->getRequest()->getParam('productsku')) {
            return $productSku;
        }
    }

}
