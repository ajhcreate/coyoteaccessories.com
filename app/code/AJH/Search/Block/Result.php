<?php

namespace AJH\Search\Block;

class Result extends \Magento\Framework\View\Element\Template {

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus
     * @param \Magento\Catalog\Model\Product\Visibility $productVisibility
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function __construct(
    \Magento\Framework\View\Element\Template\Context $context,
            \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
            \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus,
            \Magento\Catalog\Model\Product\Visibility $productVisibility,
            array $data = []
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->productStatus = $productStatus;
        $this->productVisibility = $productVisibility;
        parent::__construct($context, $data);
    }

    public function getAllProductSkus() {
        
    }

    /**
     * @return \Magento\Framework\DataObject[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getProducts() {
        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
        $collection = $this->productCollectionFactory->create();
        $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
        $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner');
        $collection->addAttributeToFilter('status', ['in' => $this->productStatus->getVisibleStatusIds()])
                ->addAttributeToFilter('visibility', ['in' => $this->productVisibility->getVisibleInSiteIds()]);

        return $collection->getItems();
    }

}
