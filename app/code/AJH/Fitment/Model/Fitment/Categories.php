<?php

namespace AJH\Fitment\Model\Fitment;

use AJH\Fitment\Model\Fitment\Products as FitmentProducts;
use AJH\Fitment\Model\Fitment as Fitment;

class Categories extends \Magento\Framework\Model\AbstractModel {

    public $_categories = array();
    public $_product_categories = array();
    private $_store_root_category = null;
    public $_product_skus = array();
    private $_parent_categories = array();
    protected $_request;
    protected $_storeid;
    protected $_categoryFactory;
    protected $_categoryRepository;
    private $_helper;
    protected $_fitmentProducts;
    protected $_fitment;

    public function __construct(\Magento\Framework\App\RequestInterface $request,
            \Magento\Catalog\Model\ResourceModel\CategoryFactory $categoryFactory,
            \Magento\Catalog\Model\CategoryRepository $categoryRepository,
            \Magento\Store\Model\StoreManagerInterface $storeManager,
            FitmentProducts $fitmentProducts, Fitment $fitment
    ) {

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeId = $storeManager->getStore()->getId();

        $helper = $objectManager->get('\AJH\Fitment\Helper\Data');

        $this->_helper = $helper;

        $this->_storeid = $storeId;
        $this->_request = $request;

        $this->_categoryFactory = $categoryFactory;
        $this->_categoryRepository = $categoryRepository;

        $this->_fitmentProducts = $fitmentProducts;

        $this->_fitment = $fitment;
    }

    public function _construct() {
        
    }

    public function getIdentities() {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getDefaultValues() {
        $values = [];

        return $values;
    }

    private function getCategoryParents($category_id) {
        $category = $this->_categoryRepository->get($category_id, $this->_storeid);
        $parent_category = $category->getParentCategory();

        $level = $parent_category->getLevel();


        array_push($this->_parent_categories, $parent_category->getId());

        if ($level > 0) {
            return $this->getCategoryParents($parent_category->getId());
        } else {
            return $this->_parent_categories;
        }
    }

    public function getProductCategories() {
        return $this->_getProductCategories();
    }

    private function _getProductCategories() {
        $store_root_category = $this->_helper->getStoreRootCategoryId(intval($this->_storeid));

        $excluded_categories = $this->_helper->getExcludedCatIds();        
        
        //if PDQ Store
        if ((int) $this->_storeid === 4) {
            $productCollection = $this->_fitmentProducts->getPdqProductCollection();
        } else {
            $productCollection = $this->_fitmentProducts->getProductCollection();
        }

        foreach ($productCollection as $product) {
            $categoryIds = $product->getCategoryIds();


            foreach ($categoryIds as $category_id) {
                if (!in_array($category_id, $excluded_categories)) {
                    $_cat = $this->_categoryRepository->get($category_id, $this->_storeid);

                    $this->_parent_categories = array();

                    $parent_category_ids = $this->getCategoryParents($category_id);

                    if (in_array($store_root_category, $parent_category_ids)) {
                        $this->_product_categories[$category_id]["id"] = $_cat->getId();
                        $this->_product_categories[$category_id]["visible"] = intval($_cat->getIncludeInMenu());
                        $this->_product_categories[$category_id]["label"] = $_cat->getUmmCatLabel();
                        $this->_product_categories[$category_id]["name"] = $_cat->getName();
                        $this->_product_categories[$category_id]["image"] = $_cat->getImageUrl();

                        if (!isset($this->_product_categories[$_cat->getId()]['products'])) {
                            $this->_product_categories[$_cat->getId()]['products'] = array();
                        }

                        if (!in_array($product->getSku(), $this->_product_categories[$_cat->getId()]['products'])) {
                            $this->_product_categories[$category_id]['products'][] = $product->getSku();
                        }
                    }
                }
            }
        }                

        return $this->_product_categories;
    }

    private function _getLevel2Category($category) {
        if ($category->getParentCategory()->getLevel() > 2) {
            $this->_getLevel2Category($category->getParentCategory());
        } else {
            return $category->getParentCategory();
        }
    }

    private function _extractFitmentCategories() {
        foreach ($this->_product_categories as $product_id => $categories) {
            $this->_getFitmentCategories($categories);
        }
    }

    private function _getFitmentCategories($categories) {
        foreach ($categories as $category_id) {
            if (in_array($this->_store_root_category, $this->_getCategoryParents($category_id))) {
                $this->setProductCategories($category_id);
            }
        }
    }

    private function setProductCategories($category_id) {
        array_push($this->_categories, $category_id);
    }

    public function getFitmentCategoryProducts() {
        $fitment_categories = array();

        foreach ($this->_categories as $_category_id) {
            $fitment_products = $this->_getFitmentCategoryProducts($_category_id);
            if (count($fitment_products) > 0) {
                $fitment_categories[$_category_id] = $fitment_products;
            }
        }
        return $fitment_categories;
    }

    private function _getFitmentCategoryProducts($_category_id) {
        $product_ids = array();
        foreach ($this->_product_categories as $product_id => $categories) {
            if (in_array($_category_id, $categories)) {
                array_push($product_ids, $product_id);
            }
        }

        return $product_ids;
    }

    private function _getCategoryParents($id) {
        $category = $this->_categoryFactory->create()->load($id);

        $category_ids = explode('/', $category->getPath());

        return $category_ids;
    }

    public function getFitmentFilters() {
        $yearId = $this->_request->getParam('year', false);
        $makeName = $this->_request->getParam('make', false);
        $modelName = $this->_request->getParam('model', false);
        $submodelName = $this->_request->getParam('submodel', false);

        $filters = array(
            'year' => $yearId,
            'make' => $makeName,
            'model' => $modelName,
            'submodel' => $submodelName
        );

        return $filters;
    }

    public function getFitmentOverview() {
        $overview = [];
        if ($this->_fitment->hasFitment()) {
            $year = $this->_fitment->_year;
            $make = $this->_fitment->_make;
            $model = $this->_fitment->_model;
            $submodel = $this->_fitment->_submodel;

            $overview = array(
                'year' => $year,
                'make' => $make,
                'model' => $model,
                'submodel' => $submodel
            );
        }

        return $overview;
    }

}
