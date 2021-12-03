<?php

namespace AJH\ProductVehicle\Block\Fitment\Tpms;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use AJH\ProductVehicle\Model\VehiclestpmsFactory as VehiclestpmsCollection;
use AJH\ProductVehicle\Model\VehiclepartsFactory as VehiclepartsCollection;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\ProductFactory;
use Magento\Store\Model\StoreManagerInterface;

class Filter extends Template {

    protected $_vehiclestpmsCollection, $_product, $_productloader, $_vehiclepartsCollection, $_storeManager, $_registry;

    public function __construct(Context $context, Registry $registry,
            VehiclestpmsCollection $vehiclestpmsCollection,
            ProductRepositoryInterface $productRepository,
            ProductFactory $productloader,
            VehiclepartsCollection $vehiclepartsCollection,
            StoreManagerInterface $storeManager) {
        parent::__construct($context);
        $this->_vehiclestpmsCollection = $vehiclestpmsCollection;
        $this->_vehiclepartsCollection = $vehiclepartsCollection;

        $this->_productRepository = $productRepository;
        $this->_registry = $registry;

        $this->_storeManager = $storeManager;

        $this->_productloader = $productloader;
    }

    protected function _construct() {
        $this->setTemplate('AJH_ProductVehicle::fitment/tpms/filter/select/options.phtml');
    }

    protected function _prepareLayout() {
        parent::_prepareLayout();

        return $this;
    }

    public function getStoreId() {
        return $this->_storeManager->getStore()->getId();
    }

    public function getProductId() {
        return $this->getRequest()->getParam('pid');
    }

    /**
     * Retrieve current product
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct() {
        if (is_null($this->_product)) {
            $this->_product = $this->_registry->registry('product');

            if (is_null($this->_product)) {
                $this->_product = $this->_productloader->create()->load($this->getProductId());
            }

            if (!$this->_product->getId()) {
                throw new LocalizedException(__('Failed to initialize product'));
            }
        }

        return $this->_product;
    }

    public function getProductSku() {
        if ($this->getProduct()) {
            return $this->getProduct()->getSku();
        } elseif ($productSku = $this->getRequest()->getParam('productsku')) {
            return $productSku;
        }
    }

    public function getFilterToLoad() {
        return $this->getRequest()->getParam('fitment');
    }

    public function getFilterOptions() {
        $sku = $this->getProductSku();
        $params = $this->getRequest()->getParams();

        $yearID = $this->getParamValue('year');
        $makeID = $this->getParamValue('make');
        $modelID = $this->getParamValue('model');
        $submodelID = $this->getParamValue('submodel');

        $filter = $this->getFilterToLoad();

        $params['make'] = isset($params['make']) ? $params['make'] : 0;
        $params['model'] = isset($params['model']) ? $params['model'] : 0;
        $params['submodel'] = isset($params['submodel']) ? $params['submodel'] : 0;


        $collection = $this->_vehiclestpmsCollection->create()->getCollection();

        if ($yearID > 0) {
            $collection->addFieldToFilter('YearID', ['eq' => $yearID]);
        }

        
        /* addFieldToFilter is in the Collection.php using $params values*/
        
//        if ($makeID > 0) {
//            $collection->addFieldToFilter('MakeID', ['eq' => $makeID]);
//        }
//
//        if ($modelID > 0) {
//            $collection->addFieldToFilter('ModelID', ['eq' => $modelID]);
//        }
//
//        if ($submodelID > 0) {
//            $collection->addFieldToFilter('SubModelID', ['eq' => $submodelID]);
//        }

        $collection->getVehicleInfoByPartNumberWithProtocol($sku, $params);


        $filter_arr = [];

        switch ($filter) {
            case 'years':
                $filter_arr = ['ID' => 'YearID', 'Name' => 'YearID'];
                break;
            case 'makes':
                $filter_arr = ['ID' => 'MakeID', 'Name' => 'MakeName'];
                break;
            case 'models':
                $filter_arr = ['ID' => 'ModelID', 'Name' => 'ModelName'];
                break;
            case 'submodels':
                $filter_arr = ['ID' => 'SubModelID', 'Name' => 'SubModelName'];
                break;
            default:
        }

        $collection->getSelect()
                ->reset(\Zend_Db_Select::COLUMNS)
                ->columns($filter_arr);

        return $collection;
    }

    private function setParam($param) {
        
    }

}
