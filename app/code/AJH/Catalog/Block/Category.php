<?php

namespace AJH\Catalog\Block;

use Magento\Catalog\Model\Category as ModelCategory;

class Category extends \Magento\Framework\View\Element\Template {

    public $_catalogCategory, $_category;

    public function __construct(
    \Magento\Framework\View\Element\Template\Context $context,
            \Magento\Catalog\Model\CategoryFactory $categoryFactory,
            \Magento\Catalog\Helper\Category $catalogCategory,
            \Magento\Catalog\Model\CategoryRepository $categoryRepository,
            ModelCategory $category, array $data = []
    ) {

        $this->_catalogCategory = $catalogCategory;
        $this->_categoryInstance = $categoryFactory->create();

        $this->_category = $category;
        
        $this->categoryRepository = $categoryRepository;

        parent::__construct($context, $data);
    }

    private function getStoreCategories() {        
        $categories = [];
        $category_ids = $this->getData("category_id");        
        $_category_ids = explode(",", $category_ids);
        
        
        foreach($_category_ids as $id){
            $categories[] = $this->getCategory($id);
        }

//        $collection = $this->_catalogCategory->getStoreCategories();
//        $storeCategories = $this->_categoryInstance->getCategories($parent_id, 2);        
        
        return $categories;
    }

    public function getCategory($categoryId) {
        $category = $this->categoryRepository->get($categoryId, $this->_storeManager->getStore()->getId());

        return $category;
    }

    public function getCategories() {
        return $this->getStoreCategories();
    }

    public function getSubCategories() {
        
    }

}
