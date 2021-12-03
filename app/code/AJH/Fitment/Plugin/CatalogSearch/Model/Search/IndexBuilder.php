<?php

namespace AJH\Fitment\Plugin\CatalogSearch\Model\Search;

use Magento\Framework\DB\Select;
use Magento\Framework\Search\Request\FilterInterface;
use Magento\Framework\Search\Request\Filter\BoolExpression;
use Magento\Framework\Search\Request\Query\Filter;
use Magento\Framework\Search\RequestInterface;
use Magento\Framework\Search\Request\QueryInterface as RequestQueryInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\App\Request\Http as HttpRequest;
use AJH\Fitment\Model\Fitment\Api as FitmentApi;

use AJH\Fitment\Model\Fitment\Products as FitmentProducts;

class IndexBuilder {

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig, $_request, $_fitmentApi;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    protected $_fitmentProducts;

    public function __construct(
    \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory, \Magento\Catalog\Model\Product\Visibility $productVisibility, \Magento\Catalog\Helper\Category $categoryHelper, \Magento\Framework\Registry $registry, HttpRequest $request, FitmentApi $fitmentApi, FitmentProducts $fitmentProducts
    ) {
        $this->storeManager = $storeManager;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_productVisibility = $productVisibility;
        $this->categoryHelper = $categoryHelper;
        $this->registry = $registry;

        $this->_request = $request;
        $this->_fitmentApi = $fitmentApi;
        $this->_fitmentProducts = $fitmentProducts;
    }

    /**
     * Build index query
     *
     * @param $subject
     * @param callable $proceed
     * @param RequestInterface $request
     * @return Select
     * @SuppressWarnings(PHPMD.UnusedFormatParameter)
     */
    public function aroundBuild($subject, callable $proceed, RequestInterface $request) {
        $select = $proceed($request);
        $storeId = $this->storeManager->getStore()->getStoreId();
        $rootCatId = $this->storeManager->getStore($storeId)->getRootCategoryId();
        $productUniqueIds = $this->getCustomCollectionQuery();

        if (count($productUniqueIds)) {
            $select->where('search_index.entity_id IN (' . join(',', $productUniqueIds) . ')');
        }

        return $select;
    }

    /**
     *
     * @return ProductIds[]
     */
    public function getCustomCollectionQuery() {

        /* get all category ids of current store */
        $websiteId = $this->storeManager->getStore()->getWebsiteId();
        $currentStoreAllCategories = $this->categoryHelper->getStoreCategories(false, true, true);

        $yearID = $this->_request->getParam("year");
        $makeID = $this->_request->getParam("make");
        $modelID = $this->_request->getParam("model");
        $submodelID = $this->_request->getParam("submodel");
        $qualifiers = $this->_request->getParam("qualifiers[]");
        $_qualifiers = $this->_request->getParam("_qualifiers[]");

        $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToSelect(array('entity_id', 'sku'));
// filter current website products
        $collection->addWebsiteFilter($websiteId);
// set visibility filter
        $collection->setVisibility($this->_productVisibility->getVisibleInSiteIds());

        $skus = $this->getSkuCollection(); //$this->_fitmentApi->retrieveVehicleParts($yearID, $makeID, $modelID, $submodelID, $qualifiers, $_qualifiers);
        $collection->addAttributeToFilter('sku', array('in' => $skus));

        $collection->addAttributeToSelect('*');


        $getProductAllIds = $collection->getAllIds();

        $getProductUniqueIds = array_unique($getProductAllIds);
        return $getProductUniqueIds;
    }

    public function getSkuCollection() {
        $storeId = $this->storeManager->getStore()->getStoreId();
        $skus = [];
        
        //if PDQ Store
        if ((int) $storeId === 4) {
            $productCollection = $this->_fitmentProducts->getPdqProductCollection();
        } else {
            $productCollection = $this->_fitmentProducts->getProductCollection();
        }
        
        foreach($productCollection as $product){
            array_push($skus, $product->getSku());
        }
        
        return $skus;
    }

}
