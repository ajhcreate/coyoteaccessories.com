<?php

namespace AJH\Fitment\Model\Fitment;

use Magento\Framework\Registry;
use Magento\Framework\Model\Context;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Framework\App\ResourceConnection;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Request\Http as RequestInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Catalog\Model\CategoryFactory;
use AJH\Fitment\Model\Fitment;
use AJH\Fitment\Model\VehiclePartsFactory;
use AJH\Fitment\Model\Cache as FitmentCache;
use AJH\Fitment\Helper\Data as FitmentHelper;
use AJH\Fitment\Model\Fitment\ResourceModel\Products as ResourceModel;
use AJH\Fitment\Model\CriteriaFactory;
use AJH\FitmentUrlRewrite\Model\SitemapFactory;

class Products extends \AJH\Fitment\Model\Fitment {

    const CACHE_TAG = 'ajh_fitment_products';

    protected $_cacheTag = 'ajh_fitment_products';
    protected $_eventPrefix = 'ajh_fitment_products';
    protected $_fitment;
    protected $_resource;
    protected $_sitemap;
    protected $_fitmentCache;
    protected $_vehicleParts;
    protected $_criteria;
    protected $_fitmentQualifiers, $_cat;
    protected $_storeManager, $_eavConfig;
    protected $_fitmentHelper, $_skus, $_request, $_params;
    protected $_productCollectionFactory, $_productCollection;
    protected $criteria;
    protected $_categoryFactory;

//    public $_year, $_make, $_makes, $_model, $_models, $_submodel, $_submodels, $_qualifiers, $_fitmentQualifiers, $_cat, $_criteria;

    public function __construct(
    Context $context, Registry $registry, FitmentHelper $fitmentHelperData,
            RequestInterface $request, ResourceConnection $resource,
            ProductCollectionFactory $productCollectionFactory,
            StoreManagerInterface $storeManager, EavConfig $eavConfig,
            FitmentCache $fitmentCache, VehiclePartsFactory $vehicleParts,
            SitemapFactory $sitemap, Fitment $fitment,
            CriteriaFactory $criteria, CategoryFactory $categoryFactory) {

        parent::__construct($context, $registry, $vehicleParts, $request, $sitemap);

        $this->_fitmentHelper = $fitmentHelperData;
//        $this->_client = $this->_fitmentHelper->_client;
        $this->_params = $this->_fitmentHelper->_params;
        $this->_request = $request;
        $this->_resource = $resource;

        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_storeManager = $storeManager;
        $this->_eavConfig = $eavConfig;

        $this->_fitmentCache = $fitmentCache;
        $this->_vehicleParts = $vehicleParts;

        $this->_fitment = $fitment;
        $this->_criteria = $criteria;

        $this->_categoryFactory = $categoryFactory;

        $this->_initFitmentParams();
    }

    protected function _construct() {
        $this->_init(ResourceModel::class);
    }

    public function getIdentities() {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getDefaultValues() {
        $values = [];

        return $values;
    }

    protected function _initFitmentParams() {
        $this->_cat = $this->_request->getParam("cat");
        $this->criteria = $this->_request->getParam("criteria", null);
//        $this->_fitmentQualifiers = $this->getFitmentQualifiers();
    }

    /**
     * 
     * @return array
     */
    public function loadFitmentSkus() {
        $skus = [];

        $collection = $this->_fitment->getPartNumbersCollection();

        foreach ($collection as $partnumber) {
            $data = $partnumber->getData();
            $skus[] = $data['PartNumber'];
        }

        return $skus;
    }

    /**
     * 
     * @return array
     */
    public function getProductCollection() {
        $productSkus = $this->loadFitmentSkus();

        $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToSelect('*')
                ->addStoreFilter($this->_storeManager->getStore()->getId())                
                ->addWebsiteFilter()
                ->addAttributeToFilter('sku', array('in' => $productSkus))
                ->addAttributeToSort('sku')
                ->addAttributeToFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
                ->addAttributeToFilter('status', array('eq' => 1));

        /**
         * @category filter
         */
        if ((int) $this->_cat) {
            $category = $this->_categoryFactory->create()->load((int) $this->_cat);
            $collection->addCategoryFilter($category);
        }
        
        return $collection;
    }

    public function getProductPartNumbers() {
        $partnumbers = [];
        $collection = $this->getProductCollection();

        foreach ($collection as $product) {
            $data = $product->getData();
            $partnumbers[] = $data['sku'];
        }

        return $partnumbers;
    }

//    public function retrieveVehicleParts() {
//        $fitment = array(
//            'YearID' => $this->_year,
//            'MakeID' => $this->_make,
//            'Make' => json_encode($this->getMake()),
//            'ModelID' => $this->_model,
//            'Model' => json_encode($this->getModel()),
//            'SubModelID' => $this->_submodel,
//            'SubModel' => json_encode($this->getSubModel()),
//            'Qualifiers' => $this->_fitmentQualifiers
//        );
//
//        $partnumbers = $this->_fitmentCache->getCachedPartNumbers($fitment);
//
//        if (is_null($partnumbers) || count($partnumbers) < 1) {
//            $partnumbers = $this->getPartNumbers();
//        }
//
//        return $partnumbers;
//    }

    /**
     * 
     * @return type
     * @throws \Exception
     */
    public function fitmentYears() {
        throw new \Exception('@deprecated AJH/Fitment/Model/Fitment/Products -> fitmentYears()');

        $fitmentYears = $this->getCachedYears();

        if (!count($fitmentYears)) {
            $this->cacheFitmentYears($fitmentYears);
        }

        return $fitmentYears;
    }

    /**
     * 
     * @param type $year
     * @return type
     * @throws \Exception
     */
    public function fitmentMakes($year) {
        throw new \Exception('@deprecated AJH/Fitment/Model/Fitment/Products -> fitmentMakes()');

        $this->_makes = $this->_fitmentCache->getCachedMakes($year);

        if (!count($this->_makes) && $year) {
            $this->_params['FitmentYear'] = $year;
            $this->_fitmentCache->cacheMakes($year, $this->_makes);
        }

        return $this->_makes;
    }

    /**
     * 
     * @param type $year
     * @param type $make
     * @return type
     * @throws \Exception
     */
    public function fitmentModels($year, $make) {
        throw new \Exception('@deprecated AJH/Fitment/Model/Fitment/Products -> fitmentModels()');

        $this->_models = $this->_fitmentCache->getCachedModels($year, $make);
        if (!count($this->_models) && $year && $make) {
            $this->_params['FitmentYear'] = $year;
            $this->_params['FitmentMakeID'] = $make;
            $this->_makeID = $make;

            $this->cacheModels($year, $make, $this->_models);
        }

        return $this->_models;
    }

    /**
     * @deprecated
     * 
     * @param type $year
     * @param type $make
     * @param type $model
     * @return type
     */
    public function fitmentSubModels($year, $make, $model) {

        throw new \Exception('@deprecated AJH/Fitment/Model/Fitment/Products -> fitmentSubModels()');

        $this->_submodels = $this->_fitmentCache->getCachedSubModels($year, $make, $model);
        if (!count($this->_submodels) && $year && $make && $model) {
            $this->_params['FitmentYear'] = $year;
            $this->_params['FitmentMakeID'] = $make;
            $this->_params['FitmentModelID'] = $model;

            $this->_modelID = $model;
            $this->cacheSubModels($year, $make, $model, $this->_submodels);
        }

        return $this->_submodels;
    }

    /**
     * 
     * @param type $year
     * @param type $make
     * @param type $model
     * @param type $submodel
     * @return array
     */
    public function fitmentQualifiers($year, $make, $model, $submodel) {
        throw new \Exception('@deprecated AJH/Fitment/Model/Fitment/Products -> fitmentQualifiers()');

        $qualifiers = $this->_fitmentCache->getCachedQualifiers($year, $make, $model, $submodel);

        if (!count($qualifiers)) {
            $this->_params['FitmentYear'] = $year;
            $this->_params['FitmentMakeID'] = $make;
            $this->_params['FitmentModelID'] = $model;
            $this->_params['FitmentSubModelID'] = $submodel;

            $this->_submodelID = $submodel;

//            $this->_skus_no_qualifiers = $this->retrieveVehicleParts($year, $make, $model, $submodel, null, null);

            $this->_fitmentCache->cacheQualifiers($year, $make, $model, $submodel, $qualifiers);
        }

        return $qualifiers;
    }

    /**
     * Todo: this function must be catered in AJH/Fitment/Model/Fitment class
     * 
     * @param type $year
     * @param type $makeID
     * @return array
     */
    public function getFitmentMakeNameByID($year, $makeID) {
        throw new \Exception('@deprecated AJH/Fitment/Model/Fitment/Products -> getFitmentMakeNameByID()');

        $make = array();

        $makes = $this->fitmentMakes($year);

        foreach ($makes as $_make) {
            if (intval($_make->MakeID) === intval($makeID)) {
                $make = $_make;
                break;
            }
        }

        return $make;
    }

    /**
     * Todo: this function must be catered in AJH/Fitment/Model/Fitment class
     * @param int $year
     * @param int $makeID
     * @param int $modelID
     * @return array
     */
    public function getFitmentModelNameByID($year, $makeID, $modelID) {

        throw new \Exception('@deprecated AJH/Fitment/Model/Fitment/Products -> getFitmentModelNameByID()');

        $model = array();

        $models = $this->fitmentModels($year, $makeID);

        foreach ($models as $_model) {
            if (intval($_model->ModelID) === intval($modelID)) {
                $model = $_model;
                break;
            }
        }

        return $model;
    }

    /**
     * Todo: this function must be catered in AJH/Fitment/Model/Fitment class
     * @param int $year
     * @param int $makeID
     * @param int $modelID
     * @param int $submodelID
     * @return array
     */
    public function getFitmentSubModelNameByID($year, $makeID, $modelID,
            $submodelID) {

        throw new \Exception('@deprecated AJH/Fitment/Model/Fitment/Products -> getFitmentSubModelNameByID()');

        $submodel = array();
        $submodels = $this->fitmentSubModels($year, $makeID, $modelID);

        if (is_array($submodels)) {
            foreach ($submodels as $_submodel) {
                if (intval($_submodel->SubModelID) === intval($submodelID)) {
                    $submodel = $_submodel;

                    break;
                }
            }
        } else {
            $submodel = $submodels;
        }

        return $submodel;
    }

    /**
     * Todo: this function must be catered in AJH/Fitment/Model/Fitment class
     * @return array
     */
    private function getFitmentQualifiers() {
        throw new \Exception('@deprecated AJH/Fitment/Model/Fitment/Products -> getFitmentQualifiers()');

        $qualifiers_str = '';
        $qualifiers_arr = [];
        $_qualifiers_arr = [];

        if (isset($this->_qualifiers) && is_array($this->_qualifiers)) {
            foreach ($this->_qualifiers as $key => $qualifier) {
                if ($key == 0) {
                    $qualifiers_arr = explode(",", $qualifier);
                }
            }
        }

        if (isset($this->_qualifiers2) && is_array($this->_qualifiers2)) {
            foreach ($this->_qualifiers2 as $key => $_qualifier) {
                if ($key == 0) {
                    $_qualifiers_arr = explode(",", $_qualifier);
                }
            }
        }

        if (count($qualifiers_arr)) {
            $_qualifiers_str = array();
            foreach ($qualifiers_arr as $key => $qualifier) {
                if ($qualifier !== '' && count($_qualifiers_arr)) {
                    if (is_array($_qualifiers_str) && isset($_qualifiers_arr[$key])) {
                        array_push($_qualifiers_str, $_qualifiers_arr[$key] . '=' . $qualifier);
                    }
                }
            }

            $qualifiers_str = implode("|", $_qualifiers_str);
        }

        return $qualifiers_str;
    }

    /**
     * Todo: move this method to AJH/Fitment/Model/PDQ
     * @return array $collection
     */
    public function getPdqProductCollection() {
//        $dbConnection = $this->_resource->getConnection('revo');
//        if ($this->criteria) {
//            $result = $dbConnection->fetchAll(sprintf('SELECT p1.partnumber, p2.partnumber as LinkedPart'
//                            . ' FROM vwvehiclestpms4 v'
//                            . ' LEFT OUTER JOIN partxref x on x.PartMasterID = v.AdditionalCriteria_PartMasterID'
//                            . ' LEFT OUTER JOIN partmaster p1 on p1.ID = v.AdditionalCriteria_PartMasterID'
//                            . ' LEFT OUTER JOIN partmaster p2 on p2.ID = x.XREF_ID'
//                            . ' WHERE v.YearID=%d and v.MakeID=%d and v.ModelID=%d and v.SubModelID=%d and AdditionalCriteria_PartMasterID=%d', $this->_year, $this->_make, $this->_model, $this->_submodel, $this->criteria
//            ));
//        } else {
//            $result = $dbConnection->fetchAll(sprintf('SELECT p1.partnumber, p2.partnumber as LinkedPart'
//                            . ' FROM vwvehiclestpms4 v'
//                            . ' LEFT OUTER JOIN partxref x on x.PartMasterID = v.PartMasterID'
//                            . ' LEFT OUTER JOIN partmaster p1 on p1.ID = v.PartMasterID'
//                            . ' LEFT OUTER JOIN partmaster p2 on p2.ID = x.XREF_ID'
//                            . ' WHERE v.YearID=%d and v.MakeID=%d and v.ModelID=%d and v.SubModelID=%d', $this->_year, $this->_make, $this->_model, $this->_submodel
//            ));
//        }
//        $_productSkus = array_unique(array_merge(array_column($result, 'partnumber'), array_column($result, 'LinkedPart')));

        $productSkus = $this->getPdqProductPartNumbers();

        $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToSelect('*')
                ->addStoreFilter($this->_storeManager->getStore()->getId())
                ->addAttributeToFilter('sku', array('in' => $productSkus))
                ->addAttributeToSort('sku')
                ->addAttributeToFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
                ->addAttributeToFilter('status', array('eq' => 1));
        
        /**
         * @category filter
         */
        if ((int) $this->_cat) {
            $category = $this->_categoryFactory->create()->load((int) $this->_cat);
            $collection->addCategoryFilter($category);
        }
        
        return $collection;
    }

    private function getPdqProductPartNumbersCollection() {

        $criteria = $this->_criteria->create();
        $_collection = $criteria->getCollection();
        $_collection->addFieldToFilter('main_table.YearID', ['eq' => $this->_year]);
        $_collection->addFieldToFilter('main_table.MakeID', ['eq' => $this->_make]);
        $_collection->addFieldToFilter('main_table.ModelID', ['eq' => $this->_model]);
        $_collection->addFieldToFilter('main_table.SubModelID', ['eq' => $this->_submodel]);

        if (trim($this->criteria)) {
            $_collection->addFieldToFilter('main_table.AdditionalCriteria_PartMasterID', ['eq' => $this->criteria]);
            $_collection->getSelect()
                    ->joinLeft(array('x' => 'partxref'), 'x.PartMasterID = main_table.PartMasterID', array('x.PartMasterID'))
                    ->joinLeft(array('p1' => 'partmaster'), 'p1.ID = main_table.AdditionalCriteria_PartMasterID', array('p1.ID', 'p1.PartNumber AS partnumber'))
                    ->joinLeft(array('p2' => 'partmaster'), 'p2.ID = x.XREF_ID', array('p2.partnumber as LinkedPart'));
        } else {
            $_collection->getSelect()->joinLeft(array('x' => 'partxref'), 'x.PartMasterID = main_table.PartMasterID', array('x.PartMasterID'))
                    ->joinLeft(array('p1' => 'partmaster'), 'p1.ID = main_table.PartMasterID', array('p1.ID', 'p1.PartNumber AS partnumber'))
                    ->joinLeft(array('p2' => 'partmaster'), 'p2.ID = x.XREF_ID', array('p2.partnumber as SKU'));
        }

//        echo $_collection->getSelect()->__toString();
//        echo $_collection->count();
//        die;

        return $_collection;
    }

    public function getPdqProductPartNumbers() {
        $partnumbers = [];
        $products = $this->getPdqProductPartNumbersCollection();

        if ($products->count()) {
            foreach ($products as $product) {
                $data = $product->getData();
                $partnumbers[] = $data['SKU'];
            }
        }

        return $partnumbers;
    }

}
