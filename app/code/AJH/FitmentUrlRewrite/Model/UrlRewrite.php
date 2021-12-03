<?php

namespace AJH\FitmentUrlRewrite\Model;

use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\UrlRewrite\Model\UrlRewriteFactory;
use Magento\Store\Model\StoreManagerInterface;
use AJH\Fitment\Model\Years;
use AJH\Fitment\Model\Fitment;
use AJH\FitmentUrlRewrite\Model\SitemapFactory;

/**
 * @deprecated AJH\Fitment\Model\Fitment\Api
 */
//use AJH\Fitment\Model\Fitment\Api as FitmentApi;

class UrlRewrite extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface {

    /**
     * @var \Magento\UrlRewrite\Model\UrlRewriteFactory
     */
    protected $_urlRewriteFactory;
    protected $_fitment;
    protected $_years;
    protected $_fitmentApi;
    protected $_fitmentUrlRewriteFactory;
    protected $_storeManager;

    public function getIdentities() {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getDefaultValues() {
        $values = [];

        return $values;
    }

    protected function _construct() {
        $this->_init('AJH\FitmentUrlRewrite\Model\ResourceModel\UrlRewrite');
    }

    /**
     * 
     * @param Context $context
     * @param Registry $registry
     * @param UrlRewriteFactory $urlRewriteFactory
     * @param Fitment $fitment
     * @param Years $years
     * @param FitmentApi $fitmentApi
     * @param SitemapFactory $sitemapFactory
     */
    public function __construct(Context $context, Registry $registry,
            UrlRewriteFactory $urlRewriteFactory, Fitment $fitment,
            Years $years, SitemapFactory $sitemapFactory,
            StoreManagerInterface $storeManager) {

        $this->_urlRewriteFactory = $urlRewriteFactory;
        $this->_fitment = $fitment;

        $this->_sitemapFactory = $sitemapFactory;

        $this->_years = $years->getYears();
//        $this->_fitmentApi = $fitmentApi;

        $this->_storeManager = $storeManager;

//        $this->_urlInterface = $urlInterface;

        parent::__construct($context, $registry);
    }

    /**
     * 
     * @return int store_id
     */
    public function getStoreId() {
        return $this->_storeManager->getStore()->getId();
    }

    /**
     * 
     * @return array() $years
     */
    public function loadYears() {
        $years = $this->_years;
        return $years;
    }

    /**
     * @param array $params
     * @return array() $makes_arr
     */
    public function loadMakes($params) {
        $makes_arr = [];

        try {

            if (!is_array($params)) {
                $params = ['year' => $params];
            }
            $makes = $this->_fitment->getMakes($params);

            foreach ($makes as $make) {
                $key = 'makeid' . $make['ID'];
                $makes_arr[$key]['MakeName'] = $make['Name'];
                $makes_arr[$key]['MakeID'] = $make['ID'];
            }
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }

        return $makes_arr;
    }

    /**
     * 
     * @param int $year
     * @param int $makeID
     * @return array() $models_arr
     */
    public function loadModels($year, $makeID) {
        $models_arr = [];
        $models = $this->_fitment->getModels($year, $makeID);

        if (is_array($models)) {
            foreach ($models as $model) {
                $key = 'modelid' . $model->ModelID;
                $models_arr[$key]['ModelName'] = $model->ModelName;
                $models_arr[$key]['ModelID'] = $model->ModelID;
            }
        } else {
            $key = 'modelid' . $models->ModelID;
            $models_arr[$key]['ModelName'] = $models->ModelName;
            $models_arr[$key]['ModelID'] = $models->ModelID;
        }

        return $models_arr;
    }

    /**
     * @param int $year
     * @param int $makeID
     * @param int $modelID
     * @return array() $submodels_arr
     */
    public function loadSubModels($year, $makeID, $modelID) {
        $submodels_arr = [];
        $submodels = $this->_fitmentApi->getSubModelsByYearMakeModel($year, $makeID, $modelID);

        if (is_array($submodels)) {
            foreach ($submodels as $submodel) {
                $key = 'submodelid' . $submodel->SubModelID;
                $submodels_arr[$key]['SubModelName'] = $submodel->SubModelName;
                $submodels_arr[$key]['SubModelID'] = $submodel->SubModelID;
            }
        } else {
            $key = 'submodelid' . $submodels->SubModelID;
            $submodels_arr[$key]['SubModelName'] = $submodels->SubModelName;
            $submodels_arr[$key]['SubModelID'] = $submodels->SubModelID;
        }

        return $submodels_arr;
    }

    /**
     * 
     * @param int $year
     * @param int $makeID
     * @param int $modelID
     * @param int $submodelID
     * @return array() $models
     */
    public function loadQualifiers($year, $makeID, $modelID, $submodelID) {
        $models = $this->_fitmentApi->getQualifiers($year, $makeID, $modelID, $submodelID);

        return $models;
    }

    /**
     * @param int $year
     * @param int $makeID
     * @param int $modelID
     * @param int $submodelID
     * @param array() $qualifiers
     * @param array() $qualifiers2
     * @return string
     */
    public function addFitmentSefUrl($params) {

        $url_params = [
            'year' => $params['yearID'],
            'make' => $params['makeID'],
            'model' => $params['modelID'],
            'submodel' => $params['submodelID'],
            'qualifiers[]' => isset($params['qualifiers'][0]) ? $params['qualifiers'][0] : '',
            '_qualifiers[]' => isset($params['qualifiers'][1]) ? $params['qualifiers'][1] : ''
        ];

        $categories_url = $this->_storeManager->getStore()->getUrl('fitment/index/categories');

        $year = $params['yearID'];
        $make = $this->_fitment->getMake($params);
        $makeName = $make['Name'];
        $model = $this->_fitment->getModel($params);
        $modelName = $model['Name'];
        $submodel = $this->_fitment->getSubModel($params);
        $submodelName = $submodel['Name'];

        $make_alias = strtolower(str_replace(" ", "_", $makeName));
        $model_alias = strtolower(str_replace(" ", "_", $modelName));
        $submodel_alias = strtolower(str_replace(" ", "_", $submodelName));

        $target_path = $categories_url . http_build_query($url_params);
        $request_path = $this->_storeManager->getStore()->getUrl("fitment/{$year}-{$make_alias}-{$model_alias}-{$submodel_alias}");

        $_model = $this->_sitemapFactory->create();
        $_model->addData([
            "year" => $year,
            "makeID" => $make['ID'],
            "make" => $makeName,
            "modelID" => $model['ID'],
            "model" => $modelName,
            "submodelID" => $submodel['ID'],
            "submodel" => $submodelName,
            "target_path" => $target_path,
            "request_path" => $request_path
        ]);

        $saveData = $_model->save();

        if ($saveData) {
            $this->addUrlRewrite($request_path, $target_path, array(1, 4));
        } else {
            $request_path = $this->_storeManager->getStore()->getUrl('fitment/index/categories') . http_build_query($url_params);
        }

        return $request_path;
    }

    /**
     * @param int $year
     * @param int $makeID
     * @param int $modelID
     * @param int $submodelID
     * @param array $qualifiers
     * @param array $qualifiers2
     * @return SEFUrl
     */
    public function getFitmentSefUrl($params = []) {

        if (empty($params)) {
            $params = $this->_fitment->getParams();
        }

        $fitmentFactory = $this->_sitemapFactory;
        $collection = $fitmentFactory->create()->getCollection();
        $collection->addFieldToFilter('YearID', ['eq' => $params['YearID']]);
        $collection->addFieldToFilter('MakeID', ['eq' => $params['MakeID']]);
        $collection->addFieldToFilter('ModelID', ['eq' => $params['ModelID']]);
        $collection->addFieldToFilter('SubModelID', ['eq' => $params['SubModelID']]);

        if ($collection->count()) {
            $url = $collection->getFirstItem();

            /**
             * Todo: check if request path url already rewrite
             */
            $sef_url = $this->isUrlRewritesExists($url->getRequestPath()) ? $url->getRequestPath() : $this->addUrlRewrite($url->getRequestPath(), $url->getTargetPath());
        } else {
            $sef_url = $this->addFitmentSefUrl($params);
        }

        return $sef_url;
    }

    /**
     * @param string $request_path
     * @param string $target_path
     * @param array() $store_ids
     *      
     * @return string $request_path
     */
    public function addUrlRewrite($request_path, $target_path, $store_ids = []) {
        $response = false;

        if (empty($store_ids)) {
            $store_id = $this->getStoreId();
            $store_ids = [$store_id];
        }

        if (!$this->isUrlRewritesExists($request_path) && is_array($store_ids) && !empty($store_ids)) {
            foreach ($store_ids as $store_id) {
                $urlRewriteModel = $this->_urlRewriteFactory->create();
                /* set current store id */
                $urlRewriteModel->setStoreId($store_id);
                /* this url is not created by system so set as 0 */
                $urlRewriteModel->setIsSystem(0);
                /* unique identifier - set random unique value to id path */
                $urlRewriteModel->setIdPath(rand(1, 100000));
                /* set actual url path to target path field */
                $urlRewriteModel->setTargetPath($target_path);
                /* set requested path which you want to create */
                $urlRewriteModel->setRequestPath($request_path);
                /* set current store id */
                $response = $urlRewriteModel->save();
            }
        }

        return $response ? $request_path : null;
    }

    /**
     * @todo add 'store' parameter in order to check URL Rewrite by store
     * @param string $requestUri
     * @return bool check if URL Rewrite already exists
     */
    private function isUrlRewritesExists($requestUri) {
        $store_id = $this->getStoreId();

        $urlrewrites = $this->_urlRewriteFactory->create();
        $collection = $urlrewrites->getCollection()->addFieldToFilter('request_path', ['eq' => $requestUri])->addFieldToFilter('store_id', ['eq' => $store_id]);

        return $collection->count() ? true : false;
    }

}
