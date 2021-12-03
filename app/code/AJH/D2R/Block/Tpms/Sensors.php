<?php

namespace AJH\D2R\Block\Tpms;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\CatalogSearch\Model\Advanced as CatalogSearchAdvanced;

//use Magento\Catalog\Model\ResourceModel\CategoryFactory;
use Magento\Framework\Registry;

use Magento\Catalog\Model\CategoryFactory;

use Magento\Framework\App\Config\ScopeConfigInterface;

use Magento\Store\Model\StoreManagerInterface;

class Sensors extends Template {

    protected $_catalogSearch, $_categoryFactory, $_registry;
    protected $_scopeConfig, $_categoryRepository;

    public function __construct(Context $context, CatalogSearchAdvanced $catalodSearch, CategoryFactory $categoryFactory, Registry $registry, ScopeConfigInterface $scopeConfig, CategoryFactory $categoryRepository, StoreManagerInterface $storeManager) {
        $this->_catalogSearch = $catalodSearch;
        $this->_categoryFactory = $categoryFactory;
        
        $this->_registry = $registry;
        
        $this->_scopeConfig = $scopeConfig;
        $this->_categoryRepository = $categoryRepository;
        $this->_storeManager = $storeManager;

        parent::__construct($context);
    }

    protected function _construct() {
        $this->setTemplate('AJH_D2R::tpms/sensors.phtml');
    }

    public function getSensors() {
        $res = [];
        if (!$categoryId = $this->getRequest()->getParam('cat')) {
            $categoryId = $this->_scopeConfig->getValue('d2r_tpms/general/sensors_category');
            $_GET['cat'] = $categoryId; // Inchoo code uses namely $_GET            
        }
        $category = $this->_categoryRepository->create()->load($categoryId);
        
        $this->_registry->register('current_category', $category);
        $this->_registry->register('catalog_part_search_type', 'results');

        $col = $this->_catalogSearch->getProductCollection()->addCategoryFilter($category);

        foreach ($col as $item) {
            $res[$item->getId()] = $item->getSku();
        }
        return $res;
    }

}
