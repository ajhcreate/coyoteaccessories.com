<?php

namespace AJH\Fitment\Model\Catalog\Product;

Class ProductList extends \AJH\Fitment\Model\Fitment\Api {

    /** @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory */
    protected $_collectionFactory;

    public function __construct(
    \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory
    ) {
        $this->_collectionFactory = $collectionFactory;
    }

    public function getFitmentParams() {
        
    }

    public function getCollection() {
        $collection = $this->_collectionFactory->create();
        $collection->addAttributeToSelect('*');
        $collection->setPageSize(3); // fetching only 3 products

       
        return $collection;
    }

}
