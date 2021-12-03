<?php

namespace AJH\Fitment\Block\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\CategoryFactory as CategoryCollection;
use Magento\Theme\Block\Html\Header\Logo;
use AJH\Fitment\Model\Fitment\Garage;
use AJH\Fitment\Model\Fitment\Categories as FitmentCategories;
use AJH\Fitment\Helper\Fitment as FitmentHelper;
use AJH\Fitment\Model\Fitment\Api as FitmentApi;
use AJH\Fitment\Model\YearsFactory as FitmentYears;
use AJH\Fitment\Model\Fitment;
use Inchoo\Search\Helper\Vehicle as SearchVehicleHelper;
use Inchoo\Search\Helper\Search as SearchSearchHelper;
use Inchoo\Search\Model\Catalogsearch\Advanced as AdvancedCatalogSearch;

class Dropdown extends Template implements BlockInterface {

    const _fitment_base_url = 'fitment/index/';

    protected $_template = "widget/dropdown.phtml";
    protected $_fitmentApi, $_storeManager, $_garage;
    protected $_fitmentCategories;
    protected $_fitmentHelper;
    protected $_year, $_make, $_model, $_submodel, $_qualifiers, $_qualifiers2;
    protected $_searchVehicleHelper;
    protected $_searchSearchHelper;
    protected $_categoryCollection;
    protected $_advancedCatalogSearch;
    private $_fitmentYears;
    private $fitment;
    protected $_logo;

    public function __construct(Context $context, FitmentApi $fitmentApi,
            StoreManagerInterface $storeManager, Garage $garage,
            FitmentCategories $fitmentCategories, FitmentHelper $fitmentHelper,
            SearchVehicleHelper $searchVehicleHelper,
            SearchSearchHelper $searchSearchHelper,
            CategoryCollection $categoryCollection,
            AdvancedCatalogSearch $advancedCatalogSearch,
            FitmentYears $fitmentYears, Fitment $fitment, Logo $logo) {
        parent::__construct($context);

        $this->_fitmentApi = $fitmentApi;
        $this->_storeManager = $storeManager;
        $this->_garage = $garage;

        $this->_fitmentHelper = $fitmentHelper;
        $this->_fitmentCategories = $fitmentCategories;

        $this->_year = $this->_request->getParam('year');
        $this->_make = $this->_request->getParam('make');
        $this->_model = $this->_request->getParam('model');
        $this->_submodel = $this->_request->getParam('submodel');

        $this->_qualifiers = $this->_request->getParam('qualifiers');
        $this->_qualifiers2 = $this->_request->getParam('_qualifiers');

        $this->_searchVehicleHelper = $searchVehicleHelper;
        $this->_searchSearchHelper = $searchSearchHelper;
        $this->_categoryCollection = $categoryCollection;

        $this->_advancedCatalogSearch = $advancedCatalogSearch;

        $this->_fitmentYears = $fitmentYears;

        $this->fitment = $fitment;

        $this->_logo = $logo;
    }

    public function _prepareLayout() {
        
    }

    public function getYears() {
        $collection = $this->fitment->getYearsCollection();
        return $collection;
    }

    public function getMakes() {
        $collection = $this->fitment->getMakesCollection();
        return $collection;
    }

    public function getModels() {
        $collection = $this->fitment->getModelsCollection();
        return $collection;
    }

    public function getSubModels() {
        $collection = $this->fitment->getSubModelsCollection();
        return $collection;
    }

    public function getQualifiers() {
        $collection = $this->fitment->getQualifiers();
        return $collection;
    }

    /**
     * 
     * @param array $params
     * @param array $attr
     * @return string
     */
    public function toSelectOptionHtml($params, $attr=[]) {
        $option = sprintf('<option value="%s" data-title="' . $params['value'] . '">%s</option>', htmlspecialchars($params['value']), htmlspecialchars($params['label']));
        return $option;
    }

    public function getStoreUrl() {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);
    }

    public function getFitmentUrls() {
        $urls = [
            'index' => $this->getUrl(self::_fitment_base_url . 'index'),
            'years' => $this->getUrl(self::_fitment_base_url . 'years'),
            'makes' => $this->getUrl(self::_fitment_base_url . 'makes'),
            'models' => $this->getUrl(self::_fitment_base_url . 'models'),
            'submodels' => $this->getUrl(self::_fitment_base_url . 'submodels'),
            'qualifiers' => $this->getUrl(self::_fitment_base_url . 'qualifiers'),
            'criteria' => $this->getUrl(self::_fitment_base_url . 'additionalcriteria'),
            'categories' => $this->getUrl(self::_fitment_base_url . 'categories')
        ];

        return json_encode($urls);
    }

    public function getGarageVehicle() {
        return $this->_garage->getGarageVehicles();
    }

    public function getFitmentOverview() {
        $overview = [];
        if ($this->_fitmentApi->hasFitment()) {
            $year = $this->_year;
            $make = $this->fitment->getMake();
            $model = $this->fitment->getModel();
            $submodel = $this->fitment->getSubModel();

            $overview = array(
                'year' => $year,
                'make' => $make,
                'model' => $model,
                'submodel' => $submodel
            );
        }

        return $overview;
    }

    public function getCategories() {
        return $this->_fitmentCategories->getProductCategories();
    }

    public function getSkusInCategories() {
        $skus = [];
        $categories = $this->getCategories();

        foreach ($categories as $category):
            $_skus = $category['products'];
            foreach ($_skus as $_sku):
                array_push($skus, $_sku);
            endforeach;
        endforeach;

        return $skus;
    }

    public function getFitmentMetrics() {
        $fitmentMetrics = $this->getDbConnection()->fetchRow(sprintf('SELECT * FROM `fitmentmetrics` WHERE fmt_year=%d AND fmt_makeid=%d AND fmt_modelid=%d AND fmt_submodelid=%d', $this->_year, $this->_make, $this->_model, $this->_submodel));

        return $fitmentMetrics;
    }

    public function getDbConnection() {
        return $this->_fitmentHelper->getDbConnection();
    }

    public function getProductSkus() {
        return $this->_fitmentApi->loadFitmentSkus();
    }

    public function getFitmentParams() {
        $filters = null;

        $qualifiers = is_array($this->_qualifiers) ? implode(",", $this->_qualifiers) : "";
        $_qualifiers = is_array($this->_qualifiers2) ? implode(",", $this->_qualifiers2) : "";

        if ($this->_year):
            $filters = array(
                'params' => "year={$this->_year}&make={$this->_make}&model={$this->_model}&submodel={$this->_submodel}&qualifiers[]=" . $qualifiers . "&_qualifiers[]=" . $_qualifiers
            );
        endif;

        return $filters;
    }

    public function getFitmentData() {
        return array(
            'fmt_option' => 'Option',
            'fmt_pcd' => 'PCD',
            'fmt_centerbore' => 'Center Bore',
            'fmt_nutorbolt' => 'Nut or Bolt',
            'fmt_thread' => 'Thread',
            'fmt_hex' => 'Hex',
            'fmt_basevehicleid' => 'Base Vehicle ID',
            'fmt_boltlength' => 'Bolt Length'
        );
    }

    public function getOeSensors() {
        $params = $this->getRequest()->getParams();
        $yearId = (int) $this->_year;
        $makeId = (int) $this->_make;
        $modelId = (int) $this->_model;
        $subModelId = (int) $this->_submodel;

        $additionalCriteriaPartmasterId = isset($params['criteria']) && !empty($params['criteria']) ? $params['criteria'] : null;
        $eoSensors = $this->_searchVehicleHelper->getOeSensors($yearId, $makeId, $modelId, $subModelId, $additionalCriteriaPartmasterId);
        if ($eoSensors && $eoSensors->getSize() > 0) {
            return $eoSensors;
        } else {
            return null;
        }
    }

    public function getOeSensorsNoResultMsg() {
        return $this->_searchVehicleHelper->getOeNoResultMsg();
    }

    public function getImageUrl($imageFileName) {
        if ($imageFileName && $imagePath = $this->_searchVehicleHelper->getImagePath()) {
            return $imagePath . '' . $imageFileName;
        }
        return '';
    }

    public function getDefCats() {
        return [
            54 => ['field' => 'Lug', 'name' => 'Lug Nuts'],
            56 => ['field' => 'Bolt', 'name' => 'Lug Bolts'],
            59 => ['field' => 'Stud', 'name' => 'Wheel Studs'],
            57 => ['field' => 'WheelLock', 'name' => 'Wheel lock'],
            61 => ['field' => 'hubBore', 'name' => 'Hub Ring'],
            5 => ['field' => 'Adapter', 'name' => 'Adapters'],
            72 => ['field' => 'hubcentric', 'name' => 'Hub centric billet spacers'],
            76 => ['field' => 'wheelspacers', 'name' => 'Wheel Spacers Die Cast'],
            18 => ['field' => 'orotekSenorsCount', 'name' => 'PDQ Sensors'],
            20 => ['field' => 'orotekServiceKitsCount', 'name' => 'PDQ Service Kits'],
            21 => ['field' => 'accessoriesCount', 'name' => 'Accessories'],
            22 => ['field' => 'triggerToolsCount', 'name' => 'Trigger & Reset Tools'],
            33 => ['field' => 'TPMSStemsCount', 'name' => 'TPMS Stems'],
        ];
    }

    public function getSearchForUrl($filterData, $catId) {

        return $this->_searchSearchHelper->getSearchForUrl($filterData, $catId);
    }

    public function getPDQFilters() {
        return array_combine(\Inchoo\Search\Model\Catalogsearch\Category::ids(array_keys($this->getDefCats())), array_values($this->getDefCats()));
    }

    public function getPDQCategories() {
        return $this->_advancedCatalogSearch->getCategories();
    }

    public function getStoreCategories() {
        
    }

    public function isHomePage() {
        return $this->_logo->isHomePage();
    }

}
