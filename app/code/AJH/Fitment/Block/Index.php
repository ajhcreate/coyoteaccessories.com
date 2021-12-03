<?php

namespace AJH\Fitment\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use AJH\Fitment\Model\Fitment\Api as FitmentApi;
use AJH\Fitment\Model\Fitment\Categories as FitmentCategories;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Session\SessionManagerInterface as CoreSession;
use Magento\Framework\App\ResourceConnection;

class Index extends Template {

    protected $_fitmentApi, $_fitmentCategories;
    public $fitment_year;
    public $fitment_make, $fitment_model, $fitment_submodel;
    
    protected $_coreSession;
    protected $_resourceConnection;

    public function __construct(Context $context, FitmentApi $_fitment, FitmentCategories $_categories, RequestInterface $request, CoreSession $coreSession, ResourceConnection $_resourceConnection) {
        $this->fitment_year = $request->getParam('year', false);
        $this->fitment_make = $request->getParam('make', false);
        $this->fitment_model = $request->getParam('model', false);
        $this->fitment_submodel = $request->getParam('submodel', false);
        
        $this->_coreSession = $coreSession;

        $this->qualifiers = $request->getParam('qualifiers', false);
        $this->_qualifiers = $request->getParam('_qualifiers', false);

        $this->_fitmentApi = $_fitment;
        $this->_fitmentCategories = $_categories->getProductCategories();   
        
        $this->_resourceConnection = $_resourceConnection;

        parent::__construct($context,array($this->_fitmentApi, $this->_fitmentCategories, $request));
    }

    public function getFitmentYears() {
        $_objectManager = \Magento\Framework\App\ObjectManager::getInstance(); //instance of\Magento\Framework\App\ObjectManager
        $fitment = $_objectManager->get('\AJH\Fitment\Model\Fitment\Api');

        return $fitment->fitmentYears();
    }

    public function getSummary() {
        $year = $this->_coreSession->getFitmentYear();
        $make = $this->_coreSession->getFitmentMake();
        $model = $this->_coreSession->getFitmentModel();
        $submodel = $this->_coreSession->getFitmentSubModel();

        $summary = array(
            'year' => $year,
            'make' => $make,
            'model' => $model,
            'submodel' => $submodel
        );

        return $summary;
    }

    public function getFitmentOverview() {
        $overview = null;
        $garage_vehicles = array();                

//        $this->_coreSession->unsFitmentGarage2();
        
//        $_objectManager = \Magento\Framework\App\ObjectManager::getInstance(); //instance of\Magento\Framework\App\ObjectManager
        $fitment = $this->_fitmentApi; //$_objectManager->get('\AJH\Fitment\Model\Fitment\Api');

        if ($fitment->hasFitment()) {
            $year = $this->fitment_year;
            $make = $fitment->getFitmentMakeName();
            $model = $fitment->getFitmentModelName();
            $submodel = $fitment->getFitmentSubModelName();

            $garage = $this->_coreSession->getFitmentGarage2();

            if (count($garage)) {
                foreach ($garage as $key => $vehicle) {
                    $garage_vehicles[$key] = $vehicle;
                }
            }

            $overview = array(
                'year' => $year,
                'make' => $make,
                'model' => $model,
                'submodel' => $submodel
            );

            $_year = $this->fitment_year;
            $_make = $this->_fitmentApi->getFitmentMakeName();
            $_model = $this->_fitmentApi->getFitmentModelName();
            $_submodel = $this->_fitmentApi->getFitmentSubModelName();

            $urlString = Mage::helper('core/url')->getCurrentUrl();
            $url = Mage::getSingleton('core/url')->parseUrl($urlString);
            $path = $url->getPath();

            $vid = $this->fitment_year . '-' . $this->fitment_make . '-' . $this->fitment_model . '-' . $this->fitment_submodel;
            $vname['id'] = $vid;
            $vname['current'] = $vid;
            $vname['name'] = $_year . ' ' . $_make['Name'] . ' ' . $_model['Name'] . ' ' . $_submodel['Name'];
            $vname['url'] = "{$path}?year={$this->fitment_year}&make={$this->fitment_make}&model={$this->fitment_model}&submodel={$this->fitment_submodel}&qualifiers[]=" . implode(",", $this->qualifiers) . "&_qualifiers[]=" . implode(",", $this->_qualifiers);

            $garage_vehicles[$vid] = $vname;
            $this->_coreSession->setFitmentGarage2($garage_vehicles);
        }

        return $overview;
    }

    public function getCategories() {
        return $this->_fitmentCategories;
    }

    public function getProductSkus() {
        return $this->_fitmentApi->loadFitmentSkus();
    }    

    public function getFitmentParams() {
        $filters = null;        
        
        if ($this->fitment_year):
            $filters = array(
                'params' => "year={$this->fitment_year}&make={$this->fitment_make}&model={$this->fitment_model}&submodel={$this->fitment_submodel}&qualifiers[]=" . implode(",", $this->qualifiers) . "&_qualifiers[]=" . implode(",", $this->_qualifiers)
            );

        endif;

        return $filters;
    }

    public function _getCategories() {
        $dbConnection = $this->_resourceConnection->getConnection('revo'); //Mage::getConfig()->getResourceConnectionConfig('externaldb_database');
        
//        var_dump(get_class_methods($config));
//        die;
//        
//        $dbConfig = array(
//            'host' => $config->host,
//            'username' => $config->username,
//            'password' => $config->password,
//            'dbname' => $config->dbname
//        );
//
//        $read = Zend_Db::factory('Pdo_Mysql', $dbConfig);
        $attributes = $dbConnection->fetchRow(sprintf('SELECT * FROM `fitmentmetrics` WHERE fmt_year=%d AND fmt_makeid=%d AND fmt_modelid=%d AND fmt_submodelid=%d', $this->fitment_year, $this->fitment_make, $this->fitment_model, $this->fitment_submodel));

        return $attributes;
    }

    public function fitmentData() {
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

    public function getMakes() {

        $yearId = $this->getRequest()->getParam('year');

        if (!$yearId) {
            die('Year is required. Please select year!');
        }

        $fitment = $this->_fitmentApi;
        $makes = $fitment->fitmentMakes($yearId);

        return $makes;
    }

    public function getModels() {
        $yearId = $this->getRequest()->getParam('year');
        $makeId = $this->getRequest()->getParam('make');

        $this->_coreSession->setCurrentFitmentMake($makeId);

        if (!$yearId || !$makeId) {
            die('Year and Make are required.');
        }

        $fitment = Mage::getSingleton('AJH_Fitment_Model_Fitment_Api');
        $models = $fitment->fitmentModels($yearId, $makeId);

        return $models;
    }

    public function getSubModels() {
        $yearId = $this->getRequest()->getParam('year');
        $makeId = $this->getRequest()->getParam('make');
        $modelId = $this->getRequest()->getParam('model');

        if (!$yearId || !$makeId || !$modelId) {
            die('Year, Make and Model are required.');
        }

        $fitment = Mage::getSingleton('AJH_Fitment_Model_Fitment_Api');
        $submodels = $fitment->fitmentSubModels($yearId, $makeId, $modelId);

        return $submodels;
    }

    private function getMake($makeId) {
        $makes = $this->getMakes();
        $make = array();

        foreach ($makes as $_make) {
            if ($_make->MakeID == $makeId) {
                $make['ID'] = $_make->MakeID;
                $make['Name'] = trim($_make->MakeName);
            }
        }

        return $make;
    }

    private function getModel($modelId) {
        $models = $this->getModels();
        $model = array();

        foreach ($models as $_model) {
            if ($_model->ModelID == $modelId) {
                $model['ID'] = $_model->ModelID;
                $model['Name'] = trim($_model->ModelName);
            }
        }

        return $model;
    }

    private function getSubmodel($submodelId) {
        $submodels = $this->getSubModels();
        $submodel = array();

        foreach ($submodels as $_submodel) {
            if ($_submodel->SubmodelID == $submodelId) {
                $submodel['ID'] = $_submodel->SubmodelID;
                $submodel['Name'] = trim($_submodel->SubmodelName);
            }
        }

        return $submodel;
    }

    public function getModelImage($modelId) {
        ini_set("allow_url_fopen", 1);

        $year = $this->getRequest()->getParam('year');
        $makeId = $this->getRequest()->getParam('make');

        $make = $this->getMake($makeId);
        $model = $this->getModel($modelId);

        $api_key = $this->getAPIKey();

        $url = "http://iconfigurators.app/api/?function=getVehicleImages&year=$year&make={$make['Name']}&model={$model['Name']}&key=$api_key";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_ENCODING, '');
        $result = curl_exec($ch);
        curl_close($ch);

        $obj = json_decode($result);

        return $this->_fitmentApi->setPlaceholderImage($obj->vehicles[0]->baseImage);
    }

    public function getSubModelImage($submodel) {
        ini_set("allow_url_fopen", 1);

        $year = $this->getRequest()->getParam('year');
        $makeId = $this->getRequest()->getParam('make');
        $modelId = $this->getRequest()->getParam('model');

        $make = $this->getMake($makeId);
        $model = $this->getModel($modelId);
//        $submodel = $this->getSubmodel($submodelId);

        $api_key = $this->getAPIKey();

        $url = "http://iconfigurators.app/api/?function=getVehicleImages&year=$year&make={$make['Name']}&model={$model['Name']}&submodel={$submodel}&key=$api_key";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_ENCODING, '');
        $result = curl_exec($ch);
        curl_close($ch);

        $obj = json_decode($result);

        if (isset($obj->vehicles[0])) {
            return $this->_fitmentApi->setPlaceholderImage($obj->vehicles[0]->baseImage);
        } else {
            return $this->_fitmentApi->setPlaceholderImage($this->getModelImage($modelId));
        }
    }

    public function getAPIKey() {
        return 'GSEPXELYCHZLIEIZPSTXSSGWTDCLYZNRYTYSIOIBTLMDGFMXUX';
    }

}
