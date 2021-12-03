<?php

namespace AJH\Fitment\Block\Catalog\Product;

//use Magento\Catalog\Model\Product;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Catalog\Model\Product\Attribute\Repository as AttributeRepository;

class ProductList extends \Magento\Catalog\Block\Product\ListProduct {

    protected $_customerSession;
    protected $categoryFactory;
    protected $productCollectionFactory;
    protected $_filterCollection;
    protected $_productAttributeRepository;
    protected $_filterable_attributes;
    protected $_collection;

    /**
     * ListProduct constructor.
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\Data\Helper\PostHelper $postDataHelper
     * @param \Magento\Catalog\Model\Layer\Resolver $layerResolver
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
     * @param \Magento\Framework\Url\Helper\Data $urlHelper
     * @param Helper $helper
     * @param array $data
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     */
    public function __construct(
    \Magento\Catalog\Block\Product\Context $context,
            \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
            \Magento\Catalog\Model\Layer\Resolver $layerResolver,
            \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
            \Magento\Framework\Url\Helper\Data $urlHelper,
            \Magento\Customer\Model\Session $customerSession,
            \Magento\Catalog\Model\CategoryFactory $categoryFactory,
            \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
            \AJH\Fitment\Plugin\CatalogSearch\Model\Search\IndexBuilder $filteredCollection,
            \Magento\Store\Model\StoreManagerInterface $storeManager,
            HttpRequest $request,
            AttributeRepository $productAttributeRepository,
            \Magento\Catalog\Model\ResourceModel\Product\Collection $collection,
            \Magento\Framework\App\ResourceConnection $resource,
            array $data = []
    ) {
        $this->_customerSession = $customerSession;
        $this->categoryFactory = $categoryFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->_filterCollection = $filteredCollection;

        $this->storeManager = $storeManager;
        $this->_request = $request;

        $this->_productAttributeRepository = $productAttributeRepository;
        $this->_filterable_attributes = [];

        $this->categoryRepository = $categoryRepository;
        $this->_collection = $collection;
        $this->_resource = $resource;

        parent::__construct($context, $postDataHelper, $layerResolver, $categoryRepository, $urlHelper, $data);
    }

    public function _getLoadedProductCollection() {        
        return $this->getProductCollection();
    }

    public function getProductCollection() {
        $productSkus = ['8170-8170N-E', '73-7256'];
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect('*');
//        $collection->addAttributeToFilter('sku', array('in' => $productSkus));
        $collection->setPageSize(10); // fetching only 10 products

//        parent::setCollection($collection);

        return $collection;
    }

//    public function setCollection($collection) {
//        parent::setCollection($collection);
//        return $this;
//    }

    private function loadFilterableAttribute($attr_code) {
        try {
            $attribute = $this->_productAttributeRepository->get($attr_code);
            if ($attribute['attribute_id'] && $attribute['is_filterable']) {
                array_push($this->_filterable_attributes, $attr_code);
            }
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            //  attribute does not exists
        }
    }

    public function getProducts() {
        $count = $this->getProductCount();
        $category_id = $this->getData("category_id");
        $collection = clone $this->_collection;
        $collection->clear()->getSelect()->reset(\Magento\Framework\DB\Select::WHERE)->reset(\Magento\Framework\DB\Select::ORDER)->reset(\Magento\Framework\DB\Select::LIMIT_COUNT)->reset(\Magento\Framework\DB\Select::LIMIT_OFFSET)->reset(\Magento\Framework\DB\Select::GROUP);

        $product_ids = $this->_filterCollection->getCustomCollectionQuery();

        if (!$category_id) {
            $category_id = $this->_storeManager->getStore()->getRootCategoryId();
        }
        $category = $this->categoryRepository->get($category_id);
        if (isset($category) && $category) {
            $collection->addMinimalPrice()
                    ->addFinalPrice()
                    ->addTaxPercents()
                    ->addAttributeToSelect('name')
                    ->addAttributeToSelect('image')
                    ->addAttributeToSelect('small_image')
                    ->addAttributeToSelect('thumbnail')
                    ->addAttributeToSelect($this->_catalogConfig->getProductAttributes())
                    ->addUrlRewrite()
                    ->addCategoryFilter($category)
                    ->addAttributeToSort('created_at', 'desc');
            $collection->addIdFilter($product_ids);
        } else {
            $collection->addMinimalPrice()
                    ->addFinalPrice()
                    ->addTaxPercents()
                    ->addAttributeToSelect('name')
                    ->addAttributeToSelect('image')
                    ->addAttributeToSelect('small_image')
                    ->addAttributeToSelect('thumbnail')
                    ->addAttributeToSelect($this->_catalogConfig->getProductAttributes())
                    ->addUrlRewrite()
                    ->addAttributeToFilter('is_saleable', 1, 'left')
                    ->addAttributeToSort('created_at', 'desc');
        }

        $collection->getSelect()
                ->order('created_at', 'desc')
                ->limit($count);

        return $collection;
    }

    public function getProductCount() {
        $limit = $this->getData("product_count");
        if (!$limit)
            $limit = 10;
        return $limit;
    }

}
