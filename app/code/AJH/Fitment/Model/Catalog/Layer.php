<?php

namespace AJH\Fitment\Model\Catalog;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\ResourceModel;

class Layer extends \Magento\Catalog\Model\Layer {

    protected $productCollectionFactory;

    /**
     * @param $context
     * @param StateFactory $layerStateFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $attributeCollectionFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product $catalogProduct
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Registry $registry
     * @param CategoryRepositoryInterface $categoryRepository
     * @param array $data
     */
    public function __construct(
    \Magento\Catalog\Model\Layer\ContextInterface $context,
            \Magento\Catalog\Model\Layer\StateFactory $layerStateFactory,
            \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $attributeCollectionFactory,
            \Magento\Catalog\Model\ResourceModel\Product $catalogProduct,
            \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
            \Magento\Store\Model\StoreManagerInterface $storeManager,
            \Magento\Framework\Registry $registry,
            CategoryRepositoryInterface $categoryRepository, array $data = []) {

        $this->productCollectionFactory = $productCollectionFactory;

        parent::__construct(
                $context, $layerStateFactory, $attributeCollectionFactory, $catalogProduct, $storeManager, $registry, $categoryRepository, $data
        );
    }

//    public function prepareProductCollection($collection)
//    {
////        $this->collectionFilter->filter($collection, $this->getCurrentCategory());
////
////        return $this;
//    }


    /**
     * Retrieve current layer product collection
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
//    public function getProductCollection() {
//        $productSkus = ['8170-8170N-E'];
//        $collection = parent::getProductCollection();        
//        $collection->addAttributeToSelect('*');
//        $collection->addAttributeToFilter('sku', array('in' => $productSkus));
//
//
//        return $collection;
//    }

    /**
     * Apply layer
     * Method is colling after apply all filters, can be used
     * for prepare some index data before getting information
     * about existing indexes
     *
     * @return \Magento\Catalog\Model\Layer
     */
//    public function apply() {  
//        $stateSuffix = '';
//        foreach ($this->getState()->getFilters() as $filterItem) {
//            $stateSuffix .= '_' . $filterItem->getFilter()->getRequestVar() . '_' . $filterItem->getValueString();
//        }
//        if (!empty($stateSuffix)) {
//            $this->_stateKey = $this->getStateKey() . $stateSuffix;
//        }
//
//        return $this;
//    }

}
