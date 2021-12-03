<?php

namespace AJH\Fitment\Model;

//use AJH\Fitment\Model\YearsFactory as YearsCollection;
//use AJH\Fitment\Model\MakesFactory as MakesCollection;
//use AJH\Fitment\Model\ModelsFactory as ModelsCollection;
//use AJH\Fitment\Model\SubModelsFactory as SubModelsCollection;
//use AJH\Fitment\Model\QualifiersFactory as QualifiersCollection;
//use Magento\Customer\Model\Session as CoreSession;

use Magento\Framework\Registry;
use Magento\Framework\Model\Context;
use AJH\Fitment\Model\VehiclePartsFactory;
use AJH\FitmentUrlRewrite\Model\SitemapFactory;
use Magento\Framework\App\Request\Http as RequestInterface;

/**
 * @var class FitmentApi
 * @deprecated 
 */
//use AJH\Fitment\Model\Fitment\Api as FitmentApi;

/**
 * @class Fitment
 * @description class that contain all Fitment methods
 * 
 */
class Fitment extends \Magento\Framework\Model\AbstractModel {

    protected $_years, $_makes, $_models, $_submodels, $_qualifiers, $_request;
    public $_year, $_make, $_model, $_submodel, $_qualifier, $_qualifier2, $qualifiers;
    private $_fitmentCache;
    protected $_sitemapFactory;
    protected $_coreSession;
    public $vehicle = [];
    protected $_vehiclePartsFactory;

    public function __construct(Context $context, Registry $registry,
            VehiclePartsFactory $vehiclePartsFactory, RequestInterface $request,
            SitemapFactory $sitemap, array $data = []) {

        $this->_vehiclePartsFactory = $vehiclePartsFactory;
        $this->_sitemapFactory = $sitemap;
        $this->_request = $request;
        $this->_coreSession = null;

        $this->setParams();

//        $this->_years = null;
//        $this->_makes = null;
//        $this->_models = null;
//        $this->_submodels = null;
//        $this->_qualifiers = null;

        parent::__construct($context, $registry);
    }

    /**
     * set/initialize fitment parameters
     * @return null
     */
    protected function setParams() {
        /**
         * if url is SEF format
         * load parameters from database
         */
        $requestUri = ltrim($this->_request->getOriginalPathInfo(), '/');
        $params = $this->_sitemapFactory->create();
        $collection = $params->getCollection()->addFieldToFilter('request_path', ['eq' => $requestUri]);

        foreach ($collection as $item) {
            $data = $item->getData();

            $this->_year = $data['YearID'];
            $this->_make = $data['MakeID'];
            $this->_model = $data['ModelID'];
            $this->_submodel = $data['SubModelID'];
        }

        if ($collection->count() < 1) {
            if ($this->_request->getParam('year')) {
                $this->_year = $this->_request->getParam('year');
            }
            if ($this->_request->getParam('make')) {
                $this->_make = $this->_request->getParam('make');
            }
            if ($this->_request->getParam('model')) {
                $this->_model = $this->_request->getParam('model');
            }
            if ($this->_request->getParam('submodel')) {
                $this->_submodel = $this->_request->getParam('submodel');
            }
            if ($this->_request->getParam('qualifiers[]')) {
                $this->_qualifier = $this->_request->getParam('qualifiers[]');
            }
            if ($this->_request->getParam('_qualifiers[]')) {
                $this->_qualifier2 = $this->_request->getParam('_qualifiers[]');
            }

            $this->qualifiers = [$this->_qualifier, $this->_qualifier2];
        }

        return null;
    }

    /**
     * @return array
     */
    public function getParams() {
        $params = [
            'YearID' => $this->_year,
            'MakeID' => $this->_make,
            'ModelID' => $this->_model,
            'SubModelID' => $this->_submodel,
            'qualifiers' => [
                $this->_qualifier,
                $this->_qualifier2
            ]
        ];

        return $params;
    }

    /**
     * 
     * @return object
     */
    public function getYearsCollection() {
        $_years = $this->_vehiclePartsFactory->create();
        $collection = $_years->getCollection();
        $collection->addFieldToSelect('YearID');
        $collection->setOrder('YearID', 'DESC');
        $collection->getSelect()->group('YearID');

        return $collection;
    }

    /**
     * 
     * @return array
     */
    public function getYears() {
        $_years = $this->getYearsCollection();
        $collection = [];

        foreach ($_years as $_year) {
            $data = $_year->getData();
            $collection[] = ['YearID' => $data['YearID']];
        }

        return $collection;
    }

    /**
     * get all makes collection with no year filter
     */
    public function getAllMakesCollection() {
        try {
            $_makes = $this->_vehiclePartsFactory->create();
            $collection = $_makes->getCollection();
            $collection->addFieldToSelect('MakeID');
            $collection->addFieldToSelect('MakeName');                        
            $collection->setOrder('MakeName', 'ASC');
            $collection->getSelect()->group('MakeID');

            return $collection;
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }

        return null;
    }

    /** get all makes with no given year
     * used in WheelAccessories site homepage slider
     * todo: this must be moved to 
     * get all Makes from cache
     * @return array
     */
    public function getAllMakes() {
        return $this->getAllMakesCollection();
    }

    /**
     * get all Years available for the Make
     * @param int $MakeID
     * @return type
     */
    public function getMakeYears($MakeID) {
        $years = array();

        if (!intval($MakeID)) {
            $MakeID = $this->_request->getParam('make');
        }

        $makes = $this->getAllMakes();

        foreach ($makes as $key => $make) {
            if ((int) $key === (int) $MakeID) {
                $years = $make['years'];
            }
        }

        return $years;
    }

    /**
     * @method getMakesCollection
     * @description get Fitment Makes based on the given Year and MakeID
     * @param array $params
     * @return array $collection
     */
    public function getMakesCollection($params = []) {

        try {

            if (!empty($params) && isset($params['YearID'])) {
                $_makes = $this->_vehiclePartsFactory->create();
                $collection = $_makes->getCollection();
                $collection->addFieldToSelect('MakeID');
                $collection->addFieldToSelect('MakeName');
                $collection->addFieldToFilter('YearID', ['eq' => $params['YearID']]);
                if (isset($params['MakeID'])) {
                    $collection->addFieldToFilter('MakeID', ['eq' => $params['MakeID']]);
                }
                $collection->setOrder('MakeName', 'ASC');
                $collection->getSelect()->group('MakeID');

                return $collection;
            } else {
                throw new \Exception('Fitment Year is required!');
            }
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }

        return null;
    }

    /**
     * @method getMakes
     * @description get Fitment Makes based on the given Year and MakeID
     * @param array $params
     * @return array
     */
    public function getMakes($params = []) {
        $makes = [];

        try {

            if (empty($params)) {
                $year = $this->_year ? $this->_year : $this->_request->getPostValue('year');
            } else {
                $year = $params['year'];
            }

            if ((int) $year) {
                $_makes = $this->_vehiclePartsFactory->create();
                $collection = $_makes->getCollection();
                $collection->addFieldToSelect('YearID');
                $collection->addFieldToSelect('MakeID');
                $collection->addFieldToSelect('MakeName');
                $collection->addFieldToFilter('YearID', ['eq' => $year]);
                $collection->setOrder('MakeName', 'ASC');
                $collection->getSelect()->group('MakeID');

                $makes = $this->_getMakes($collection);
            }
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }


        return $makes;
    }

    /**
     * @method _getMakes
     * @description load Fitment Makes to an array
     * @param object $collection
     * @return array
     */
    private function _getMakes($collection) {
        $makes = [];

        if ($collection->count() > 0) {
            foreach ($collection as $_make) {
                $make = [];
                $data = $_make->getData();
                if (isset($data['MakeID'])) {
                    $make['YearID'] = $data['YearID'];
                    $make['ID'] = $data['MakeID'];
                    $make['MakeID'] = $data['MakeID'];
                    $make['Name'] = $data['MakeName'];
                    $make['MakeName'] = $data['MakeName'];
                    $make['Logo'] = isset($data['MakeLogo']) ? $data['MakeLogo'] : '';
                    $make['MakeLogo'] = isset($data['MakeLogo']) ? $data['MakeLogo'] : '';

                    $makes[] = $make;
                }
            }
        }

        return $makes;
    }

    /**
     * @method getMake
     * @description get specific Fitment Make     
     * @param array $params
     * @return array
     */
    public function getMake($params = []) {
        $make = [];


        try {

            if (empty($params) || !isset($params['YearID']) || !isset($params['MakeID'])) {
                $params = ['YearID' => $this->_year, 'MakeID' => $this->_make];
            }

            $year = $params['YearID'];
            $MakeID = $params['MakeID'];

            if ((int) $year && (int) $MakeID) {
                $collection = $this->getMakesCollection($params);
//                $collection = $makes->getCollection()
//                        ->addFieldToFilter('YearID', ['eq' => $year])
//                        ->addFieldToFilter('MakeID', ['eq' => $MakeID]);
//                echo $collection->getSelect()->__toString();                

                $make = $this->_getMake($collection);
            } else {
                throw new \Exception('Fitment Year and Make are required!');
            }
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }

        return $make;
    }

    /**
     * @name _getMake
     * @description get first item of the collection
     * @param type $collection
     * @return array
     */
    private function _getMake($collection) {
        $make = [];

        if ($collection->count() > 0) {
            $firstCollectionItem = $collection->getFirstItem();
            $data = $firstCollectionItem->getData();

            if (count($data) && isset($data['MakeID'])) {
                $make['ID'] = $data['MakeID'];
                $make['Name'] = $data['MakeName'];
                $make['Logo'] = isset($data['MakeLogo']) ? $data['MakeLogo'] : '';
            }
        }

        return $make;
    }

    /**
     * @method getModelsCollection
     * @description get Fitment Models collection based on the given Year and MakeID
     * @param array $params
     * @return array $collection
     */
    public function getModelsCollection($params = []) {

        try {

            if (!empty($params) && isset($params['YearID']) && isset($params['MakeID'])) {
                $_models = $this->_vehiclePartsFactory->create();
                $collection = $_models->getCollection();
                $collection->addFieldToSelect('MakeID');
                $collection->addFieldToSelect('MakeName');
                $collection->addFieldToSelect('ModelID');
                $collection->addFieldToSelect('ModelName');
                $collection->addFieldToFilter('YearID', ['eq' => $params['YearID']]);
                $collection->addFieldToFilter('MakeID', ['eq' => $params['MakeID']]);
                if (isset($params['ModelID'])) {
                    $collection->addFieldToFilter('ModelID', ['eq' => $params['ModelID']]);
                }
                $collection->setOrder('ModelName', 'ASC');
                $collection->getSelect()->group('ModelID');

                return $collection;
            } else {
                throw new \Exception('Fitment Year and Make are required!');
            }
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }

        return null;
    }

    /**
     * @method getModels
     * @description get Fitment Models based on the given Year and MakeID
     * @param array $param
     * @return array
     */
    public function getModels($param = []) {
        $models = [];

        try {

            if (!is_null($param) && isset($param['YearID']) && isset($param['MakeID'])) {
                $year = $param['YearID'];
                $MakeID = $param['MakeID'];
            } else {
                $year = $this->_year;
                $MakeID = $this->_make;
            }

            if ((int) $year && (int) $MakeID) {
                $_models = $this->_vehiclePartsFactory->create();
                $collection = $_models->getCollection();
                $collection->addFieldToSelect('YearID');
                $collection->addFieldToSelect('MakeID');
                $collection->addFieldToSelect('ModelID');
                $collection->addFieldToSelect('ModelName');
                $collection->addFieldToFilter('YearID', ['eq' => $year]);
                $collection->addFieldToFilter('MakeID', ['eq' => $MakeID]);
                $collection->setOrder('ModelName', 'ASC');
                $collection->getSelect()->group('ModelID');

                $models = $this->_getModels($collection);
            } else {
                throw new \Exception('Fitment Year and MakeID are required!');
            }
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }

        return $models;
    }

    /**
     * @method _getModels
     * @description load Fitment Models to an array
     * @param object $collection
     * @return array
     */
    private function _getModels($collection) {
        $models = [];

        if ($collection->count() > 0) {
            foreach ($collection as $_model) {
                $model = [];
                $data = $_model->getData();
                if (isset($data['ModelID'])) {
                    $model['YearID'] = $data['YearID'];
                    $model['MakeID'] = $data['MakeID'];
                    $model['ModelID'] = $data['ModelID'];
                    $model['ID'] = $data['ModelID'];
                    $model['ModelName'] = $data['ModelName'];
                    $model['Name'] = $data['ModelName'];

                    $models[] = $model;
                }
            }
        }

        return $models;
    }

    /**
     * @method getModel
     * @description get specific Fitment Model     
     * @param array $params
     * @return array
     */
    public function getModel($params = []) {
        $model = [];

        try {
            if (empty($params) || !isset($params['YearID']) || !isset($params['MakeID']) || !isset($params['ModelID'])) {
                $params = ['YearID' => $this->_year, 'MakeID' => $this->_make, 'ModelID' => $this->_model];
            }

            $year = $params['YearID'];
            $MakeID = $params['MakeID'];
            $ModelID = $params['ModelID'];

            if ((int) $year && (int) $MakeID && (int) $ModelID) {
                $collection = $this->getModelsCollection($params);
                $model = $this->_getModel($collection);
            } else {
                throw new \Exception('Fitment Year, Make and Model are required!');
            }
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }

        return $model;
    }

    /**
     * @name _getModel
     * @description get first item of the collection
     * @param type $collection
     * @return array
     */
    private function _getModel($collection) {
        $model = [];

        if ($collection->count() > 0) {
            $firstCollectionItem = $collection->getFirstItem();
            $data = $firstCollectionItem->getData();

            if (count($data) && isset($data['ModelID'])) {
                $model['ID'] = $data['ModelID'];
                $model['Name'] = $data['ModelName'];
            }
        }

        return $model;
    }

    /**
     * @method getSubModelsCollection
     * @description get Fitment SubModels collection based on the given parameters
     * @param array $params
     * @return array $collection
     */
    public function getSubModelsCollection($params = []) {

        try {

            if (!empty($params) && isset($params['YearID']) && isset($params['MakeID']) && isset($params['ModelID'])) {
                $_models = $this->_vehiclePartsFactory->create();
                $collection = $_models->getCollection();
                $collection->addFieldToSelect('YearID');
                $collection->addFieldToSelect('MakeID');
                $collection->addFieldToSelect('ModelID');
                $collection->addFieldToSelect('SubModelID');
                $collection->addFieldToSelect('SubModelName');

                $collection->addFieldToFilter('YearID', ['eq' => $params['YearID']]);
                $collection->addFieldToFilter('MakeID', ['eq' => $params['MakeID']]);
                $collection->addFieldToFilter('ModelID', ['eq' => $params['ModelID']]);

                if (isset($params['SubModelID'])) {
                    $collection->addFieldToFilter('SubModelID', ['eq' => $params['SubModelID']]);
                }
                $collection->setOrder('SubModelName', 'ASC');
                $collection->getSelect()->group('SubModelID');

                return $collection;
            } else {
                throw new \Exception('Fitment Year, Make and Model are required!');
            }
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }

        return null;
    }

    /**
     * @method getSubModels
     * @description get Fitment SubModels based on the given YearID and MakeID
     * @param array $params
     * @return array
     */
    public function getSubModels($params = []) {
        $submodels = [];

        try {

            if (!is_null($params) && isset($params['YearID']) && isset($params['MakeID']) && isset($params['ModelID'])) {
                $year = $params['YearID'];
                $MakeID = $params['MakeID'];
                $ModelID = $params['ModelID'];
            } else {
                $params['YearID'] = $year = $this->_year;
                $params['MakeID'] = $MakeID = $this->_make;
                $params['ModelID'] = $ModelID = $this->_model;
            }

            if ((int) $year && (int) $MakeID && (int) $ModelID) {
                $collection = $this->getSubModelsCollection($params);
                $submodels = $this->_getSubModels($collection);
            } else {
                throw new \Exception('Fitment Year, Make and Model are required!');
            }
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }

        return $submodels;
    }

    /**
     * @method _getSubModels
     * @description load Fitment SubModels to an array
     * @param object $collection
     * @return array
     */
    private function _getSubModels($collection) {
        $submodels = [];

        if ($collection->count() > 0) {
            foreach ($collection as $_submodel) {
                $data = $_submodel->getData();
                if (isset($data['SubModelID'])) {
                    $data['ID'] = $data['SubModelID'];
                    $data['Name'] = $data['SubModelName'];
                    $submodels[] = $data;
                }
            }
        }

        return $submodels;
    }

    /**
     * @method getSubModel
     * @description get specific Fitment SubModel     
     * @param array $params
     * @return array
     */
    public function getSubModel($params = []) {
        $submodel = [];

        try {
            if (empty($params) || !isset($params['YearID']) || !isset($params['MakeID']) || !isset($params['ModelID']) || !isset($params['SubModelID'])) {
                $params = ['YearID' => $this->_year, 'MakeID' => $this->_make, 'ModelID' => $this->_model, 'SubModelID' => $this->_submodel];
            }

            $year = $params['YearID'];
            $MakeID = $params['MakeID'];
            $ModelID = $params['ModelID'];
            $SubModelID = $params['SubModelID'];

            if ((int) $year && (int) $MakeID && (int) $ModelID && (int) $SubModelID) {
                $collection = $this->getSubModelsCollection($params);
                $submodel = $this->_getSubModel($collection);
            } else {
                throw new \Exception('Fitment Year, Make, Model and SubModel are required!');
            }
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }

        return $submodel;
    }

    /**
     * 
     * @param array $collection
     * @return array
     */
    private function _getSubModel($collection) {
        $submodel = [];

        if ($collection->count() > 0) {
            $firstCollectionItem = $collection->getFirstItem();
            $data = $firstCollectionItem->getData();

            if (count($data) && isset($data['SubModelID'])) {
                $submodel['ID'] = $data['SubModelID'];
                $submodel['Name'] = $data['SubModelName'];
            }
        }

        return $submodel;
    }

    /**
     * @method getQualifiers
     * @description get Fitment Qualifiers based on the given YearID, MakeID, ModelID and SubModelID
     * @param array $params
     * @return array
     */
    public function getQualifiers($params = []) {
        $_qualifiers = [];

        try {
            if (!is_null($params) && isset($params['YearID']) && isset($params['MakeID'])) {
                $year = $params['YearID'];
                $MakeID = $params['MakeID'];
                $ModelID = $params['ModelID'];
                $SubModelID = $params['SubModelID'];
            } else {
                $year = $this->_year;
                $MakeID = $this->_make;
                $ModelID = $this->_model;
                $SubModelID = $this->_submodel;
            }

            if ((int) $year && (int) $MakeID && (int) $ModelID && (int) $SubModelID) {
                $qualifiers = $this->_vehiclePartsFactory->create();
                $collection = $qualifiers->getCollection();
                $collection->addFieldToSelect('YearID');
                $collection->addFieldToSelect('MakeID');
                $collection->addFieldToSelect('ModelID');
                $collection->addFieldToSelect('SubModelID');
                $collection->addFieldToSelect('RegionID');
                $collection->addFieldToSelect('RegionName');
                $collection->addFieldToSelect('DriveTypeID');
                $collection->addFieldToSelect('DriveTypeName');

                $collection->addFieldToFilter('YearID', ['eq' => $year]);
                $collection->addFieldToFilter('MakeID', ['eq' => $MakeID]);
                $collection->addFieldToFilter('ModelID', ['eq' => $ModelID]);
                $collection->addFieldToFilter('SubModelID', ['eq' => $SubModelID]);
                $collection->getSelect()->group('RegionName');

                $_qualifiers = $this->_getQualifiers($collection);

//                $this->vehicle = array('year' => $year, 'make' => $MakeID, 'model' => $ModelID, 'submodel' => $SubModelID);
//                $this->_coreSession->start();
//                $this->_coreSession->setSelectedVehicle($this->vehicle);
            } else {
                throw new \Exception('Fitment Year, Make and Model are required!');
            }
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }

        return $_qualifiers;
    }

    /**
     * @method _getQualifiers
     * @description set qualifiers options to array
     * @param object $collection
     * @return array
     */
    private function _getQualifiers($collection) {
        $qualifiers = [];

        if ($collection->count() > 0) {
            foreach ($collection as $_collection) {
                $data = $_collection->getData();
                if (count($data)) {
                    $data['QualifierID'] = $data['YearID'] . $data['MakeID'] . $data['ModelID'] . $data['SubModelID'] . $data['RegionID'] . $data['DriveTypeID'];
                    $qualifiers[] = $data;
                }
            }
        }

        return $qualifiers;
    }

    /**
     * @method getQualifer
     * @description return selected qualifiers from the fitment filter select
     * @param type $param
     */
    public function getQualifier($param = []) {

        $this->_qualifier = $this->_request->getParam('qualifiers');
        $this->_qualifier2 = $this->_request->getParam('_qualifiers');


        $qualifer = [$this->_qualifier, $this->_qualifier2];

        return $qualifer;
    }

    /**
     * @method _getQualifiersArray
     * @description set qualifiers options to array
     * @param object $data resource collection
     * @param array $keys Allowed qualifiers
     * @return array
     */
    private function _getQualifiersArray($data, $keys) {
        $qualifiers = [];
        $indx = 0;
        foreach ($data as $_key => $_data) {
            if (is_array($_data)) {
                foreach ($_data as $key => $qualifier) {
                    if (array_key_exists($key, $keys)) {
                        $qualifiers[$key][$qualifier] = array("label" => $keys[$key]["label"], "value" => $qualifier, "option" => $_data[$keys[$key]["option"]]);
                    }
                }
            }
            $indx++;
        }

        return $qualifiers;
    }

    /**
     * 
     * @return string
     */
    private function getFitmentQualifiers() {
        $qualifiers_arr = [];
        $_qualifiers_arr = [];

        if (isset($this->_qualifier) && is_array($this->_qualifier)) {
            foreach ($this->_qualifier as $key => $qualifier) {
                if ($key == 0) {
                    $qualifiers_arr = explode(",", $qualifier);
                }
            }
        }

        if (isset($this->_qualifier2) && is_array($this->_qualifier2)) {
            foreach ($this->_qualifier2 as $key => $_qualifier) {
                if ($key == 0) {
                    $_qualifiers_arr = explode(",", $_qualifier);
                }
            }
        }

        $_qualifiers_str = array();
        foreach ($qualifiers_arr as $key => $qualifier) {
            if ($qualifier !== '' && count($_qualifiers_arr) && is_array($_qualifiers_str) && isset($_qualifiers_arr[$key])) {
                array_push($_qualifiers_str, $_qualifiers_arr[$key] . '=' . $qualifier);
            }
        }

        $qualifiers_str = implode("|", $_qualifiers_str);

        return $qualifiers_str;
    }

    /**
     * @method currentlySelectedVehicle
     * @description get selected fitment vehicle from session
     * @return object
     */
    public function currentlySelectedVehicle() {
        $this->_coreSession->start();
        $vehicle = $this->_coreSession->getSelectedVehicle();

        return $vehicle;
    }

    /**
     * @method hasFitment
     * @description check if all fitment parameters are selected
     * @return boolean
     */
    public function hasFitment() {
        $SubModelID = $this->_submodel;

        if (intval($SubModelID)) {
            return true;
        }

        return false;
    }

    /**
     * 
     * @param array $params
     * @return array $_skus
     */
    public function getPartNumbersCollection($params = []) {

        try {

            if (empty($params) || isset($params['YearID']) || isset($params['MakeID']) || isset($params['ModelID']) || isset($params['SubModelID'])) {
                $params['YearID'] = $this->_year;
                $params['MakeID'] = $this->_make;
                $params['ModelID'] = $this->_model;
                $params['SubModelID'] = $this->_submodel;
            }

            if (!empty($params) && isset($params['YearID']) && isset($params['MakeID']) && isset($params['ModelID']) && isset($params['SubModelID'])) {
                $_models = $this->_vehiclePartsFactory->create();
                $collection = $_models->getCollection();
                $collection->addFieldToSelect('YearID');
                $collection->addFieldToSelect('MakeID');
                $collection->addFieldToSelect('ModelID');
                $collection->addFieldToSelect('SubModelID');
                $collection->addFieldToSelect('RegionID');
                $collection->addFieldToSelect('DriveTypeID');
                $collection->addFieldToSelect('PartNumber');

                $collection->addFieldToFilter('YearID', ['eq' => $params['YearID']]);
                $collection->addFieldToFilter('MakeID', ['eq' => $params['MakeID']]);
                $collection->addFieldToFilter('ModelID', ['eq' => $params['ModelID']]);
                $collection->addFieldToFilter('SubModelID', ['eq' => $params['SubModelID']]);

                /**
                 * Todo: add Qualifiers filter
                 */
                return $collection;
            } else {
                throw new \Exception('Fitment Year, Make, Model and SubModel are required!');
            }
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }

        return null;
    }

    /**
     * 
     * @param array $params
     * @return array $_skus
     */
    public function getPartNumbers($params = []) {
        $partnumbers = [];

        try {
            if (empty($params) || !isset($params['YearID']) || !isset($params['MakeID']) || !isset($params['ModelID']) || !isset($params['SubModelID'])) {
                $params = ['YearID' => $this->_year, 'MakeID' => $this->_make, 'ModelID' => $this->_model, 'SubModelID' => $this->_submodel];
            }

            $year = $params['YearID'];
            $MakeID = $params['MakeID'];
            $ModelID = $params['ModelID'];
            $SubModelID = $params['SubModelID'];

            if ((int) $year && (int) $MakeID && (int) $ModelID && (int) $SubModelID) {
                $collection = $this->getPartNumbersCollection($params);
                $partnumbers = $this->_getPartNumbers($collection);
            } else {
                throw new \Exception('Fitment Year, Make, Model and SubModel are required!');
            }
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }


//        $partnumbers = array(
//            'YearID' => $this->_year,
//            'MakeID' => $this->_make,
//            'Make' => json_encode($this->getMake()),
//            'ModelID' => $this->_model,
//            'Model' => json_encode($this->getModel()),
//            'SubModelID' => $this->_submodel,
//            'SubModel' => json_encode($this->getSubModel()),
//            'Qualifiers' => $this->_qualifiers
//        );
//
//        $parts = $this->_vehiclePartsFactory->create();
//        $collection = $parts->getCollection()
//                ->addFieldToSelect('PartNumber')
//                ->addFieldToFilter('YearID', ['eq' => $this->_year])
//                ->addFieldToFilter('MakeID', ['eq' => $this->_make])
//                ->addFieldToFilter('ModelID', ['eq' => $this->_model])
//                ->addFieldToFilter('SubModelID', ['eq' => $this->_submodel]);
//
//        foreach ($collection as $_part) {
//            $part = $_part->getData();
//
//            if (!in_array($part['PartNumber'], $_skus)) {
//                array_push($_skus, $part['PartNumber']);
//            }
//        }
//        $partnumbers['skus'] = serialize($_skus);

        return array_merge(['PartNumbers' => $partnumbers], $params);
    }

    /**
     * 
     * @param array $collection
     * @return array
     */
    private function _getPartNumbers($collection) {
        $partnumbers = [];

        if ($collection->count() > 0) {
            foreach ($collection as $_collection) {
                $data = $_collection->getData();
                array_push($partnumbers, $data['PartNumber']);
            }
        }

        return $partnumbers;
    }

}
