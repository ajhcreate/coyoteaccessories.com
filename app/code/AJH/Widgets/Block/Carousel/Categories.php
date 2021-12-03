<?php

namespace AJH\Widgets\Block\Carousel;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Catalog\Model\CategoryFactory as CategoryCollection;

use Magento\Store\Model\StoreManagerInterface;

class Categories extends Template implements BlockInterface {

    protected $_template = "carousel/categories.phtml";
    protected $_categoryCollection;

    public function __construct(Context $context,
            StoreManagerInterface $storeManager,
            CategoryCollection $categoryCollection) {
        parent::__construct($context);

        $this->_categoryCollection = $categoryCollection;
        $this->_storeManager = $storeManager;
    }

    public function getStoreCategories() {
        $currentStore = $this->_storeManager->getStore();
        $storeCategories = $this->_categoryCollection->create()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('is_active', 1)
                ->setStore($currentStore);

//        foreach ($storeCategories as $category) {
//            echo $category->getId() . '<br />';
//            echo $category->getName() . '<br />';
//            echo $category->getUrl() . '<br />';
//        }
//
//        die('categories');
        return $storeCategories;        
    }

}
