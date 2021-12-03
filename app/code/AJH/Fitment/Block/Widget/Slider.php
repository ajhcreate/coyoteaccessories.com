<?php

namespace AJH\Fitment\Block\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;
use AJH\Fitment\Model\Fitment;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use AJH\Fitment\Helper\Fitment as FitmentHelper;

class Slider extends Template implements BlockInterface {

    protected $_template = "widget/slider/makes.phtml";
    protected $fitment, $_storeManager, $_scopeConfig, $_fitmentHelper;
    public $year, $make, $model;

    public function __construct(Context $context, Fitment $fitment, StoreManagerInterface $storeManager, ScopeConfigInterface $scopeConfig, FitmentHelper $fitmentHelper) {
        parent::__construct($context);

        $this->fitment = $fitment;
        $this->_storeManager = $storeManager;
        $this->_scopeConfig = $scopeConfig;
        $this->_fitmentHelper = $fitmentHelper;
        
        $this->year = $this->getRequest()->getParam('year');
        $this->make = $this->getRequest()->getParam('make');
        $this->model = $this->getRequest()->getParam('model');
    }

    public function getFitmentYears() {
        return $this->fitment->getYears();
    }

    public function getFitmentMakes() {
        $makes = [];
        $collection = $this->fitment->getAllMakes();
        
        foreach($collection as $make){
            $data = $make->getData();
            $makes[] = $data;
        }
        return $makes;
    }

    public function getFitmentModels() {
        return $this->fitment->getModels();
    }

    public function getFitmentSubModels() {
        return $this->fitment->getSubModels();
    }

    public function getModelImage($modelId) {
        ini_set("allow_url_fopen", 1);

        $year = $this->getRequest()->getParam('year');
        $makeId = $this->getRequest()->getParam('make');

        $make = $this->getMake($makeId);
        $model = $this->getModel($modelId);

        $api_key = $this->_fitmentHelper->_ApiKey;

        $url = "https://iconfigurators.app/api/?function=getVehicleImages&year=$year&make={$make['Name']}&model={$model['Name']}&key=$api_key";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_ENCODING, '');
        $result = curl_exec($ch);
        curl_close($ch);

        $obj = json_decode($result);
        $_image_url = null;

        if (isset($obj->vehicles)) {
            foreach ($obj->vehicles as $vehicle) {
                $_image_url = $vehicle->baseImage;
            }
        }

        return $this->_fitmentHelper->setPlaceholderImage($_image_url);
    }   
    
    public function getSubModelImage($submodel) {
        ini_set("allow_url_fopen", 1);

        $year = $this->getRequest()->getParam('year');
        $makeId = $this->getRequest()->getParam('make');
        $modelId = $this->getRequest()->getParam('model');

        $make = $this->getMake($makeId);
        $model = $this->getModel($modelId);

        $api_key = $this->_fitmentHelper->_ApiKey;

        $url = "https://iconfigurators.app/api/?function=getVehicleImages&year=$year&make={$make['Name']}&model={$model['Name']}&submodel={$submodel}&key=$api_key";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_ENCODING, '');
        $result = curl_exec($ch);
        curl_close($ch);

        $obj = json_decode($result);

        $_image_url = null;

        if (isset($obj->vehicles)) {
            foreach ($obj->vehicles as $vehicle) {
                $_image_url = $vehicle->baseImage;
            }
        }

        return $this->_fitmentHelper->setPlaceholderImage($_image_url);
    }

    private function getMake($makeId) {
        $makes = $this->getMakes();
        $make = array();

        foreach ($makes as $_make) {
            if ($_make['ID'] == $makeId) {
                $make['ID'] = $_make['ID'];
                $make['Name'] = trim($_make['Name']);
            }
        }

        return $make;
    }

    public function getMakes() {

        $makes = $this->fitment->getMakes();

        return $makes;
    }

    private function getModel($modelId) {
        $models = $this->getModels();
        $model = array();

        foreach ($models as $_model) {
            if ($_model['ID'] == $modelId) {
                $model['ID'] = $_model['ID'];
                $model['Name'] = trim($_model['Name']);
            }
        }

        return $model;
    }

    public function getModels() {        
        $models = $this->fitment->getModels();

        return $models;
    }

    public function getSubModels() {
        $submodels = $this->fitment->getSubModels();

        return $submodels;
    }

    public function getStoreUrl() {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);
    }

    public function getImage($image_url) {
        $image_exists = $this->_fitmentHelper->imageExists($image_url);

        $placeholder_image_url = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product/placeholder/';
        $placeholder_image_url .= $this->_scopeConfig->getValue('catalog/placeholder/alt_image_placeholder');

        $_image_url = "http://" . preg_replace('#^https?://#', '', rtrim($image_url, '/'));

        if ($image_exists) {
            return $_image_url;
        } else {
            return $placeholder_image_url;
        }
    }

    public function getStoreManagerData() {
        echo $this->_storeManager->getStore()->getId() . '<br />';

        // by default: URL_TYPE_LINK is returned
        echo $this->_storeManager->getStore()->getBaseUrl() . '<br />';

        echo $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB) . '<br />';
        echo $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_DIRECT_LINK) . '<br />';
        echo $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . '<br />';
        echo $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_STATIC) . '<br />';

        echo $this->_storeManager->getStore()->getUrl('product/33') . '<br />';

        echo $this->_storeManager->getStore()->getCurrentUrl(false) . '<br />';

        echo $this->_storeManager->getStore()->getBaseMediaDir() . '<br />';

        echo $this->_storeManager->getStore()->getBaseStaticDir() . '<br />';
    }

    public function getMakeYears() {
        $makeID = $this->getRequest()->getParam('make');               

        return $this->fitment->getMakeYears($makeID);
    }

}
