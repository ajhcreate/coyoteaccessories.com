<?php

namespace AJH\ProductVehicle\Block\Search\Header;

use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\Element\Template;
use Magento\Framework\UrlInterface;
use AJH\ProductVehicle\Helper\Data as ProductVehicleHelperData;
use Magento\Framework\App\ResourceConnection;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\Registry;

class Form extends Template {

    protected $_urlInterface;
    protected $_helperData, $_registry, $_product;

    public function __construct(Context $context, UrlInterface $urlInterface,
            ProductVehicleHelperData $helperData, ProductFactory $productloader,
            ResourceConnection $_resourceConnection, Registry $registry) {
        parent::__construct($context);

        $this->_urlInterface = $urlInterface;
        $this->_helperData = $helperData;
        
        $this->_registry = $registry;
        $this->_productloader = $productloader;

        $this->_resourceConnection = $_resourceConnection;
    }

    protected function _construct() {
//        $isSearchEnabled = $this->_helperData->isSearchEnabled();
//        if ($isSearchEnabled) {
        $this->setTemplate('AJH_ProductVehicle::search/header/form.phtml');
//        }
    }

    /**
     * Retrieve search form url
     * @return string
     */
    public function getFormUrl() {
        return $this->_urlInterface->getUrl('vehicle/search/byparts');
    }

    public function getSearchQuery() {
        return $this->_helperData->getEscapedQueryText();
    }

    public function getDbConnection() {
        $dbConnection = $this->_resourceConnection->getConnection('revo');

        return $dbConnection;
    }

    public function getYears() {
        return $this->getDbConnection()->fetchCol('SELECT DISTINCT YearID FROM aaia_basevehicle ORDER BY YearID DESC');
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
    
        public function getProductId() {
        return $this->getRequest()->getParam('pid');
    }

}
