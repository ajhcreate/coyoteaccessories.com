<?php

namespace AJH\LayeredNavigation\Model;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory as AttributeCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;

use AJH\Fitment\Model\Fitment;
use AJH\Fitment\Model\Fitment\Products as FitmentProducts;

class Layer extends \Magento\Catalog\Model\Layer {

    protected $_fitment, $_fitmentProducts, $_storeid;

    public function __construct(
    \Magento\Catalog\Model\Layer\ContextInterface $context,
            \Magento\Catalog\Model\Layer\StateFactory $layerStateFactory,
            AttributeCollectionFactory $attributeCollectionFactory,
            \Magento\Catalog\Model\ResourceModel\Product $catalogProduct,
            \Magento\Store\Model\StoreManagerInterface $storeManager,
            \Magento\Framework\Registry $registry,
            CategoryRepositoryInterface $categoryRepository,
            CollectionFactory $productCollectionFactory, Fitment $fitment, FitmentProducts $fitmentProducts,
            array $data = []
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->_fitment = $fitment;
        $this->_fitmentProducts = $fitmentProducts;

        $storeId = $storeManager->getStore()->getId();
        $this->_storeid = $storeId;

        parent::__construct(
                $context, $layerStateFactory, $attributeCollectionFactory, $catalogProduct, $storeManager, $registry, $categoryRepository, $data
        );
    }

    public function getProductCollection() {

        if (isset($this->_productCollections['ajh_custom'])) {
            $collection = $this->_productCollections['ajh_custom'];
        } else {
            $partnumbers = [];
            //here you assign your own custom collection of products
            //if PDQ Store
            if ((int) $this->_storeid === 4) {
                $partnumbers = $this->_fitmentProducts->getPdqProductPartNumbers();
            } else {
                $partnumbers = $this->_fitmentProducts->getProductPartNumbers();
            }     

            $collection = $this->productCollectionFactory->create();
            $this->prepareProductCollection($collection);

            $collection->addAttributeToFilter('sku', array('in' => $partnumbers));
            $this->_productCollections['ajh_custom'] = $collection;

//            echo $collection->getSelect()->__toString();
//            echo $collection->count();
//            die;
        }
        return $collection;
    }

}
