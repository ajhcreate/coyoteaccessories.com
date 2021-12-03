<?php

namespace AJH\Fitment\Block;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\App\ResourceConnection;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Session\SessionManagerInterface as CoreSession;
use AJH\Fitment\Model\Fitment;
use AJH\Fitment\Model\MetricsFactory;
use AJH\FitmentUrlRewrite\Model\SitemapFactory;
use AJH\Fitment\Model\Fitment\Categories as FitmentCategories;

/**
 * @deprecated
 */
use Inchoo\Search\Helper\Vehicle as SearchVehicleHelper;

class Categories extends Template {

    protected $fitment, $_fitmentCategoriesModel;
    public $_year;
    public $_make;
    public $_model;
    public $_submodel;
    public $qualifiers;
    public $_qualifiers;
    protected $_coreSession;
    protected $_resourceConnection;
    protected $_storeManager;
    protected $_sitemapFactory;
    protected $_searchVehicleHelper;
    protected $_metricsFactory;

    public function __construct(Context $context, Fitment $fitment,
            FitmentCategories $_categories, RequestInterface $request,
            CoreSession $coreSession, ResourceConnection $_resourceConnection,
            StoreManagerInterface $storeManager, SitemapFactory $sitemapFactory,
            SearchVehicleHelper $searchVehicleHelper,
            MetricsFactory $metricsFactory) {

        $this->_sitemapFactory = $sitemapFactory;
        $this->_searchVehicleHelper = $searchVehicleHelper;

        $this->fitment = $fitment;

        $this->_metricsFactory = $metricsFactory;
//        
        $this->_year = $this->fitment->_year;
        $this->_make = $this->fitment->_make;
        $this->_model = $this->fitment->_model;
        $this->_submodel = $this->fitment->_submodel;

        $this->qualifiers = $request->getParam('qualifiers');
        $this->_qualifiers = $request->getParam('_qualifiers');

        if ($request->getParam('qualifiers') && !is_array($request->getParam('qualifiers')) && $request->getParam('qualifiers') != '') {
            $this->qualifiers = explode(",", $request->getParam('qualifiers'));
        }

        if ($request->getParam('_qualifiers') && !is_array($request->getParam('qualifiers')) && $request->getParam('_qualifiers') != '') {
            $this->_qualifiers = explode(",", $request->getParam('_qualifiers'));
        }

        $this->_coreSession = $coreSession;

        $this->_resourceConnection = $_resourceConnection;

        $this->_fitmentCategoriesModel = $_categories;

        $this->_storeManager = $storeManager;

        parent::__construct($context);
    }

    protected function _prepareLayout() {
        $title = $this->pageConfig->getTitle()->getDefault();
        $title .= ' - ' . $this->getPageTitle();

        $this->pageConfig->getTitle()->set(__($title));
        return parent::_prepareLayout();
    }

    public function getPageTitle() {
//        $fitment = $this->getFitmentFilters();
        $fitment = $this->getFitmentOverview();

        $title = $fitment['year'] . ' ' . $fitment['make']['Name'] . ' ' . $fitment['model']['Name'] . ' ' . $fitment['submodel']['Name'];

        return $title;
    }

    public function getSidebar() {
        $sidebarBlock = $this->getLayout()->createBlock('AJH\Fitment\Block\Categories\Manufacturer');

        return $sidebarBlock->toHtml();
    }

    public function getCategories() {
        return $this->_fitmentCategoriesModel->getProductCategories();
    }

    public function getFitmentFilters() {

        if ($this->_year) {
            $isAjax = $this->getRequest()->getParam('isajax');
            $makes = $this->fitment->getMakes();
            $models = $this->fitment->getModels();
            $submodels = $this->fitment->getSubModels();

            $make = $makes;

            if (is_array($makes)) {
                foreach ($makes as $_make) {
                    if ($_make['ID'] == $this->_make) {
                        $make = $_make;
                    }
                }
            }

            if (is_array($models)) {
                foreach ($models as $_model) {
                    if ($_model['ID'] == $this->_model) {
                        $model = $_model;
                    }
                }
            } else {
                $model = $models;
            }

            if (is_array($submodels)) {
                foreach ($submodels as $_submodel) {
                    if ($_submodel['ID'] == $this->_submodel) {
                        $submodel = $_submodel;
                    }
                }
            } else {
                $submodel = $submodels;
            }

            $filters = array(
                'year' => $this->_year,
                'make' => $make,
                'model' => $model,
                'submodel' => $submodel,
                'qualifiers' => $this->qualifiers,
                'isAjax' => $isAjax,
                'params' => array(
                    'year' => $this->_year,
                    'make' => $this->_make,
                    'model' => $this->_model,
                    'submodel' => $this->_submodel,
                    'qualifiers[]' => isset($this->qualifiers) && is_array($this->qualifiers) ? implode(",", $this->qualifiers) : null,
                    '_qualifiers[]' => isset($this->_qualifiers) && is_array($this->_qualifiers) ? implode(",", $this->_qualifiers) : null
                )
            );

            return $filters;
        } else {
            return;
        }
    }

    public function getFitmentParamsUrl() {
        $filter = $this->getFitmentFilters();
        $params = $filter['params'];
        $params_count = count($params);

        $query_str = "";
        $join = "&";

        $x = 1;

        if ($params_count) {
            foreach ($params as $key => $value) {
                $query_str .= $key . "=" . $value;
                if ($x < $params_count) {
                    $query_str .= $join;
                }
                $x++;
            }
        }

        return $query_str;
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

    public function getImageUrl($imageFileName) {
        if ($imageFileName && $imagePath = $this->_searchVehicleHelper->getImagePath()) {
            return $imagePath . '' . $imageFileName;
        }
        return '';
    }

    public function getFitmentOverview() {
        $overview = [];
        if ($this->fitment->hasFitment()) {
            $year = $this->fitment->_year;
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

    public function getSkusInCategories() {
        $skus = [];

        $categories = $this->_fitmentCategoriesModel->getProductCategories();

        foreach ($categories as $category):
            $_skus = $category['products'];
            foreach ($_skus as $_sku):
                array_push($skus, $_sku);
            endforeach;
        endforeach;

        return $skus;
    }

    public function getFitmentMetrics() {
        $year = $this->fitment->_year;
        $makeid = $this->fitment->_make;
        $modelid = $this->fitment->_model;
        $submodelid = $this->fitment->_submodel;

        $collection = $this->_metricsFactory->create()->getCollection();
        $collection->addFieldToFilter('fmt_year', ['eq' => $year]);
        $collection->addFieldToFilter('fmt_makeid', ['eq' => $makeid]);
        $collection->addFieldToFilter('fmt_modelid', ['eq' => $modelid]);
        $collection->addFieldToFilter('fmt_submodelid', ['eq' => $submodelid]);

//        echo $collection->getSelect()->__toString();

        return $collection;
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

    public function getCategoryUrl($path, $params = []) {
        return $path;
    }

    public function getFitmentTitle() {
        return $this->getPageTitle();
    }

}
