<?php

namespace AJH\ProductVehicle\Block\Product\Search;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use AJH\ProductVehicle\Helper\Data as DataHelper;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\View\Element\Template\Context;

class AJH_ProductVehicle_Block_Product_Search_Form extends Template {

    protected $_helper, $_productRepository, $_vehiclestpmsCollection, $_customerSession, $_partclassCollection, $_date, $_registry, $_inchooSearchHelper;

    public function __construct(Context $context, DataHelper $helper, Registry $registry, ProductRepositoryInterface $productRepository) {
        parent::__construct($context);

        $this->_helper = $helper;
        $this->_registry = $registry;
        $this->_productRepository = $productRepository;
    }

    protected function _construct() {
        $isSearchEnabled = $this->_helper->isEnabled();
        if ($isSearchEnabled) {
            $this->setTemplate('AJH_ProductVehicle::search/form.phtml');
        }
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
