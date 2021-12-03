<?php

namespace AJH\Fitment\Model;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\App\RequestInterface;
use AJH\Fitment\Model\Fitment;
use AJH\Fitment\Helper\Data as HelperData;
use AJH\Fitment\Model\Cache\YearsFactory as CacheYears;
use AJH\Fitment\Model\Cache\MakesFactory as CacheMakes;
use AJH\Fitment\Model\Cache\ModelsFactory as CacheModels;
use AJH\Fitment\Model\Cache\PartNumbersFactory as CachePartNumbers;
use AJH\Fitment\Model\Cache\SubModelsFactory as CacheSubModels;
use AJH\Fitment\Model\Cache\QualifiersFactory as CacheQualifiers;

class Cache extends \Magento\Framework\Model\AbstractModel {

    protected $_resource;
    protected $_fitment;
    protected $_request;
    protected $_makes;
    protected $_yearsCache, $_makesCache, $_modelsCache, $_submodelsCache, $_qualifiersCache, $_partNumbersCache;
    protected $_helper;

    public function __construct(ResourceConnection $resource,
            RequestInterface $request, Fitment $fitment, CacheYears $yearsCache,
            CacheMakes $makesCache, CacheModels $modelsCache,
            CacheSubModels $submodelsCache, CacheQualifiers $qualifiersCache,
            CachePartNumbers $partNumbersCache, HelperData $helper) {

        $this->_resource = $resource;
        $this->_request = $request;
        $this->_fitment = $fitment;

        $this->_yearsCache = $yearsCache;
        $this->_makesCache = $makesCache;
        $this->_modelsCache = $modelsCache;
        $this->_submodelsCache = $submodelsCache;
        $this->_qualifiersCache = $qualifiersCache;

        $this->_partNumbersCache = $partNumbersCache;

        $this->_helper = $helper;
    }

    public function cacheFitmentParams($params) {
        switch ($params['fitmentToCache']) {
            case 'years':
                $this->cacheYears($params);
                break;
            case 'makes':
                $this->cacheMakes($params);
                break;
            case 'models':
                $this->cacheModels($params);
                break;
            case 'submodels':
                $this->cacheSubModels($params);
                break;
            case 'qualifiers':
                $this->cacheQualifiers($params);
                break;
            case 'partnumbers':
                $this->cachePartNumbers($params);
                break;
            default :
                echo 'A fitment $param is required!';
        }
    }

    public function cacheYears($years = []) {
        $fitment_years = $this->_fitment->getYears();
        $cached_years = $this->getCachedYears();

        try {
            foreach ($fitment_years as $key => $year) {
                if (!array_key_exists($year['YearID'], $cached_years)) {
                    $year['id'] = null;
                    $model = $this->_yearsCache->create();
                    $model->setData($year)->save();

                    $this->_helper->fitmentLog('Successfully cached Fitment year: ' . $year['YearID']);
                }
            }
        } catch (\Exception $e) {
            $this->_helper->fitmentLog('Error caching Fitment Year:' . $e);
        }
    }

    public function cacheMakes() {

        $fitment_makes = $this->_fitment->getMakes();
        $cached_makes = $this->getCachedMakes();

        try {
            foreach ($fitment_makes as $key => $make) {
                if (!array_key_exists($make['MakeID'], $cached_makes)) {
                    $make['Make'] = json_encode($make);
                    $make['id'] = null;
                    $model = $this->_makesCache->create();
                    $model->setData($make)->save();

                    $this->_helper->fitmentLog('Successfully cached Fitment make: ' . $make['MakeName'] . '-' . $make['MakeID']);
                }
            }
        } catch (\Exception $e) {
            $this->_helper->fitmentLog('Error caching Fitment Make:' . $e);
        }
    }

    public function cacheModels() {

        $fitment_models = $this->_fitment->getModels();
        $cached_models = $this->getCachedModels();

        try {
            foreach ($fitment_models as $key => $model) {
                if (!array_key_exists($model['ModelID'], $cached_models)) {
                    $model['Model'] = json_encode($model);
                    $model['id'] = null;
                    $_model = $this->_modelsCache->create();
                    $_model->setData($model)->save();

                    $this->_helper->fitmentLog('Successfully cached Fitment model: ' . $model['ModelID'] . '-' . $model['ModelName']);
                }
            }
        } catch (\Exception $e) {
            $this->_helper->fitmentLog('Error caching Fitment Model:' . $e);
        }
    }

    /**
     * @description: save SubModel to Cache table
     */
    public function cacheSubModels() {

        try {
            $fitment_submodels = $this->_fitment->getSubModels();
            $cached_submodels = $this->getCachedSubModels();

            foreach ($fitment_submodels as $key => $submodel) {
                if (!array_key_exists($submodel['SubModelID'], $cached_submodels)) {
                    $submodel['SubModel'] = json_encode($submodel);
                    $submodel['id'] = null;
                    $model = $this->_submodelsCache->create();
                    $model->setData($submodel)->save();

                    $this->_helper->fitmentLog('Successfully cached Fitment model: ' . $submodel['SubModelID'] . '-' . $submodel['SubModelName']);
                }
            }
        } catch (\Exception $e) {
            $this->_helper->fitmentLog('Error caching Fitment SubModel:' . $e);
        }
    }

    /**
     * @description: save Qualifiers to Cache table
     */
    public function cacheQualifiers() {

        try {
            $fitment_qualifiers = $this->_fitment->getQualifiers();
            $cached_qualifiers = $this->getCachedQualifiers();

            foreach ($fitment_qualifiers as $key => $qualifier) {
                $qualifierID = $qualifier['YearID'] . $qualifier['MakeID'] . $qualifier['ModelID'] . $qualifier['SubModelID'] . $qualifier['RegionID'] . $qualifier['DriveTypeID'];

                if (!array_key_exists($qualifier['QualifierID'], $cached_qualifiers)) {
                    $qualifier['QualifierID'] = (int) $qualifierID;
                    $qualifier['Qualifiers'] = json_encode($qualifier);
                    $qualifier['id'] = null;

                    $model = $this->_qualifiersCache->create();
                    $model->setData($qualifier)->save();

                    $this->_helper->fitmentLog('Successfully cached Fitment Qualifier: ' . $qualifier['QualifierID'] . '-' . $qualifier['Qualifiers']);
                }
            }
        } catch (\Exception $e) {
            $this->_helper->fitmentLog('Error caching Fitment Qualifier:' . $e);
        }
    }

    /**
     * @description: save Criteria to Cache table
     */
    public function cacheCriteria() {

        try {
            $fitment_criteria = $this->_fitment->getCriteria();
            $cached_criteria = $this->getCachedCriteria();

            foreach ($fitment_criteria as $key => $criterion) {
//                $qualifier_id = $qualifier['YearID'] . $qualifier['MakeID'] . $qualifier['ModelID'] . $qualifier['SubModelID'];
                if (!array_key_exists($criterion['CriteriaID'], $cached_criteria)) {
                    $criterion['Criteria'] = json_encode($criterion);
                    $criterion['id'] = null;
                    $model = $this->_criteriaCache->create();
                    $model->setData($criterion)->save();

                    $this->_helper->fitmentLog('Successfully cached Fitment Criteria: ' . $criterion['CriteriaID'] . '-' . $criterion['Criteria']);
                }
            }
        } catch (\Exception $e) {
            $this->_helper->fitmentLog('Error caching Fitment Criteria:' . $e);
        }
    }

    /**
     * 
     * @return object $collection
     */
    private function getCachedYearsCollection() {

        $years = $this->_yearsCache->create();
        $collection = $years->getCollection();

        return $collection;
    }

    /**
     * 
     * @return array $collection
     */
    private function getCachedYears() {
        $collection = [];
        $years = $this->getCachedYearsCollection();

        foreach ($years as $key => $year) {
            $data = $year->getData();
            $collection[$data['YearID']] = ['YearID' => $data['YearID']];
        }

        return $collection;
    }

    /**
     * 
     * @param array $params
     * @return object $collection
     */
    private function getCachedMakesCollection($params = []) {
        try {
            $year = isset($params['YearID']) ? $params['YearID'] : $this->_fitment->_year;
            $makes = $this->_makesCache->create();
            $collection = $makes->getCollection();
            $collection->addFieldToFilter('YearID', ['eq' => $year]);
            $collection->setOrder('MakeName', 'ASC');
            $collection->getSelect()->group('MakeID');

            return $collection;
        } catch (\Exception $e) {
            $this->_helper->fitmentLog('getCachedMakesCollection Error:' . $e);
        }
    }

    /**
     * @param array $params 
     * @param array $params['YearID] is required
     * @return array
     */
    public function getCachedMakes($params = []) {
        $_params = [];
        $collection = [];

        $_params['YearID'] = isset($params['YearID']) ? $params['YearID'] : $this->_fitment->_year;

        try {
            $makes = $this->getCachedMakesCollection($_params);

            foreach ($makes as $key => $make) {
                $data = $make->getData();
                $collection[$data['MakeID']] = ['YearID' => $data['YearID'], 'MakeID' => $data['MakeID'], 'MakeName' => $data['MakeName'], 'MakeLogo' => $data['MakeLogo']];
            }

            return $collection;
        } catch (\Exception $e) {
            $this->_helper->fitmentLog('getCachedMakesCollection Error:' . $e);
        }
    }

    /**
     * @param array $params 
     * @param array $params['YearID] is required
     * @return array
     */
    public function getMakes($params = []) {
        $params = [];

        $params['YearID'] = isset($params['YearID']) ? $params['YearID'] : $this->_fitment->_year;

        try {
            $collection = $this->getCachedMakesCollection($params);

            return $this->_getCachedMakes($collection);
        } catch (\Exception $e) {
            $this->_helper->fitmentLog('getCachedMakesCollection Error:' . $e);
        }
    }

    /**
     * 
     * @return array
     */
    private function _getCachedMakes($collection) {
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
     * @param array $params
     * @return object
     */
    private function getCachedModelsCollection($params = []) {
        try {
            $year = isset($params['YearID']) ? $params['YearID'] : $this->_fitment->_year;
            $make = isset($params['MakeID']) ? $params['MakeID'] : $this->_fitment->_make;

            $models = $this->_modelsCache->create();
            $collection = $models->getCollection();
            $collection->addFieldToFilter('YearID', ['eq' => $year]);
            $collection->addFieldToFilter('MakeID', ['eq' => $make]);

            return $collection;
        } catch (\Exception $e) {
            $this->_helper->fitmentLog('getCachedModelsCollection Error:' . $e);
        }
    }

    /**
     * @param array $params 
     * @param array $params['YearID] is required
     * @return array
     */
    public function getCachedModels($params = []) {
        $collection = [];

        try {
            $params['YearID'] = isset($params['YearID']) ? $params['YearID'] : $this->_fitment->_year;
            $params['MakeID'] = isset($params['MakeID']) ? $params['MakeID'] : $this->_fitment->_make;
            $models = $this->getCachedModelsCollection($params);

            foreach ($models as $key => $model) {
                $data = $model->getData();
                $collection[$data['ModelID']] = ['YearID' => $data['YearID'], 'MakeID' => $data['MakeID'], 'ModelID' => $data['ModelID'], 'ModelName' => $data['ModelName'], 'Model' => $data['Model']];
            }

            return $collection;
        } catch (\Exception $e) {
            $this->_helper->fitmentLog('getCachedModels Error:' . $e);
        }
    }

    /**
     * @param array $params 
     * @param array $params['YearID] is required
     * @return array
     */
    public function getModels($params = []) {
        $params = [];

        $params['YearID'] = isset($params['YearID']) ? $params['YearID'] : $this->_fitment->_year;
        $params['MakeID'] = isset($params['MakeID']) ? $params['MakeID'] : $this->_fitment->_make;

        try {
            $collection = $this->getCachedModelsCollection($params);

            return $this->_getCachedModels($collection);
        } catch (\Exception $e) {
            $this->_helper->fitmentLog('getCachedMakesCollection Error:' . $e);
        }
    }

    /**
     * 
     * @return array
     */
    private function _getCachedModels($collection) {
        $models = [];

        if ($collection->count() > 0) {
            foreach ($collection as $_model) {
                $model = [];
                $data = $_model->getData();
                if (isset($data['MakeID'])) {
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
     * @param array $params
     * @return object
     */
    private function getCachedSubModelsCollection($params = []) {
        try {
            $year = isset($params['YearID']) ? $params['YearID'] : $this->_fitment->_year;
            $make = isset($params['MakeID']) ? $params['MakeID'] : $this->_fitment->_make;
            $model = isset($params['ModelID']) ? $params['ModelID'] : $this->_fitment->_model;

            $submodels = $this->_submodelsCache->create();
            $collection = $submodels->getCollection();
            $collection->addFieldToFilter('YearID', ['eq' => $year]);
            $collection->addFieldToFilter('MakeID', ['eq' => $make]);
            $collection->addFieldToFilter('ModelID', ['eq' => $model]);

            return $collection;
        } catch (\Exception $e) {
            $this->_helper->fitmentLog('getCachedSubModelsCollection Error:' . $e);
        }
    }

    /**
     * 
     * @param array $params
     * @return array $collection
     */
    public function getCachedSubModels($params = []) {
        $collection = [];

        try {
            $params['YearID'] = isset($params['YearID']) ? $params['YearID'] : $this->_fitment->_year;
            $params['MakeID'] = isset($params['MakeID']) ? $params['MakeID'] : $this->_fitment->_make;
            $params['ModelID'] = isset($params['ModelID']) ? $params['ModelID'] : $this->_fitment->_model;

            $submodels = $this->getCachedSubModelsCollection($params);

            foreach ($submodels as $key => $submodel) {
                $data = $submodel->getData();
                $collection[$data['SubModelID']] = ['YearID' => $data['YearID'], 'MakeID' => $data['MakeID'], 'ModelID' => $data['ModelID'], 'SubModelID' => $data['SubModelID'], 'SubModelName' => $data['SubModelName']];
            }

            return $collection;
        } catch (\Exception $e) {
            $this->_helper->fitmentLog('getCachedSubModels Error:' . $e);
        }
    }

    /**
     * @param array $params 
     * @param array $params['YearID] is required
     * @return array
     */
    public function getSubModels($params = []) {
        try {
            $_params = [];

            $_params['YearID'] = isset($params['YearID']) ? $params['YearID'] : $this->_fitment->_year;
            $_params['MakeID'] = isset($params['MakeID']) ? $params['MakeID'] : $this->_fitment->_make;
            $_params['ModelID'] = isset($params['ModelID']) ? $params['ModelID'] : $this->_fitment->_model;

            $collection = $this->getCachedSubModelsCollection($_params);

            return $this->_getCachedSubModels($collection);
        } catch (\Exception $e) {
            $this->_helper->fitmentLog('getSubModels Error:' . $e);
        }
    }

    /**
     * 
     * @return array
     */
    private function _getCachedSubModels($collection) {
        $submodels = [];

        try {
            if ($collection->count() > 0) {
                foreach ($collection as $_submodel) {
                    $submodel = [];
                    $data = $_submodel->getData();
                    if (isset($data['MakeID'])) {
                        $submodel['YearID'] = $data['YearID'];
                        $submodel['MakeID'] = $data['MakeID'];
                        $submodel['ModelID'] = $data['ModelID'];
                        $submodel['SubModelID'] = $data['SubModelID'];
                        $submodel['ID'] = $data['SubModelID'];
                        $submodel['SubModelName'] = $data['SubModelName'];
                        $submodel['Name'] = $data['SubModelName'];

                        $submodels[] = $submodel;
                    }
                }
            }
        } catch (\Exception $e) {
            $this->_helper->fitmentLog('_getCachedSubModels Error:' . $e);
        }

        return $submodels;
    }

    /**
     * @param array $params
     * @return object
     */
    private function getCachedQualifiersCollection($params = []) {
        try {
            $year = isset($params['YearID']) ? $params['YearID'] : $this->_fitment->_year;
            $make = isset($params['MakeID']) ? $params['MakeID'] : $this->_fitment->_make;
            $model = isset($params['ModelID']) ? $params['ModelID'] : $this->_fitment->_model;
            $submodel = isset($params['SubModelID']) ? $params['SubModelID'] : $this->_fitment->_submodel;

            $qualifiers = $this->_qualifiersCache->create();
            $collection = $qualifiers->getCollection();
            $collection->addFieldToFilter('YearID', ['eq' => $year]);
            $collection->addFieldToFilter('MakeID', ['eq' => $make]);
            $collection->addFieldToFilter('ModelID', ['eq' => $model]);
            $collection->addFieldToFilter('SubModelID', ['eq' => $submodel]);

//            echo $collection->getSelect()->__toString();

            return $collection;
        } catch (\Exception $e) {
            $this->_helper->fitmentLog('getCachedQualifiersCollection Error:' . $e);
        }
    }

    /**
     * 
     * @param array $params
     * @return array
     */
    private function getCachedQualifiers($params = []) {
        $collection = [];

        try {
            $params['YearID'] = isset($params['YearID']) ? $params['YearID'] : $this->_fitment->_year;
            $params['MakeID'] = isset($params['MakeID']) ? $params['MakeID'] : $this->_fitment->_make;
            $params['ModelID'] = isset($params['ModelID']) ? $params['ModelID'] : $this->_fitment->_model;
            $params['SubModelID'] = isset($params['SubModelID']) ? $params['SubModelID'] : $this->_fitment->_submodel;

            $qualifiers = $this->getCachedQualifiersCollection($params);

            foreach ($qualifiers as $key => $qualifier) {
                $data = $qualifier->getData();
                $collection[$data['QualifierID']] = ['QualifierID' => $data['QualifierID'], 'YearID' => $data['YearID'], 'MakeID' => $data['MakeID'], 'ModelID' => $data['ModelID'], 'SubModelID' => $data['SubModelID'], 'Qualifiers' => $data['Qualifiers']];
            }

            return $collection;
        } catch (\Exception $e) {
            $this->_helper->fitmentLog('getCachedQualifiers Error:' . $e);
        }

        return $collection;
    }

    /**
     * @param array $params 
     * @param array $params['YearID] is required
     * @return array
     */
    public function getQualifiers($params = []) {
        try {
            $_params = [];

            $_params['YearID'] = isset($params['YearID']) ? $params['YearID'] : $this->_fitment->_year;
            $_params['MakeID'] = isset($params['MakeID']) ? $params['MakeID'] : $this->_fitment->_make;
            $_params['ModelID'] = isset($params['ModelID']) ? $params['ModelID'] : $this->_fitment->_model;
            $_params['SubModelID'] = isset($params['SubModelID']) ? $params['SubModelID'] : $this->_fitment->_submodel;

            $collection = $this->getCachedQualifiersCollection($_params);

            return $this->_getCachedQualifiers($collection);
        } catch (\Exception $e) {
            $this->_helper->fitmentLog('getQualifiers Error:' . $e);
        }
    }

    /**
     * 
     * @return array
     */
    private function _getCachedQualifiers($collection) {
        $qualifiers = [];

        try {

            if ($collection->count() > 0) {
                foreach ($collection as $_collection) {
                    $data = $_collection->getData();                    
//                        $data['QualifierID'] = $data['YearID'] . $data['MakeID'] . $data['ModelID'] . $data['SubModelID'] . $data['RegionID'] . $data['DriveTypeID'];
                        $qualifiers[] = (array)json_decode($data['Qualifiers']);                    
                }
            }
        } catch (\Exception $e) {
            $this->_helper->fitmentLog('_getCachedSubModels Error:' . $e);
        }

        return $qualifiers;
    }

    /**
     * @param array $params
     * @return object
     */
    private function getCachedCriteriaCollection($params = []) {
        try {
            $year = isset($params['YearID']) ? $params['YearID'] : $this->_fitment->_year;
            $make = isset($params['MakeID']) ? $params['MakeID'] : $this->_fitment->_make;
            $model = isset($params['ModelID']) ? $params['ModelID'] : $this->_fitment->_model;
            $submodel = isset($params['SubModelID']) ? $params['SubModelID'] : $this->_fitment->_submodel;

            $fitmentID = $year . $make . $model . $submodel;

            $criteria = $this->_criteriaCache->create();
            $collection = $criteria->getCollection();
            $collection->addFieldToFilter('FitmentID', ['eq' => $fitmentID]);

            return $collection;
        } catch (\Exception $e) {
            $this->_helper->fitmentLog('getCachedCriteriaCollection Error:' . $e);
        }
    }

    /**
     * 
     * @param array $params
     * @return array
     */
    private function getCachedCriteria($params = []) {
        $collection = [];

        try {
            $params['YearID'] = isset($params['YearID']) ? $params['YearID'] : $this->_fitment->_year;
            $params['MakeID'] = isset($params['MakeID']) ? $params['MakeID'] : $this->_fitment->_make;
            $params['ModelID'] = isset($params['ModelID']) ? $params['ModelID'] : $this->_fitment->_model;
            $params['SubModelID'] = isset($params['ModelID']) ? $params['ModelID'] : $this->_fitment->_model;

            $criteria = $this->getCachedCriteriaCollection($params);

            foreach ($criteria as $key => $criterion) {
                $data = $criterion->getData();
                $collection[$data['CriteriaID']] = ['Question' => $data['Question'], 'Answer' => $data['Answer'], 'FitmentID' => $data['FitmentID'], 'SEFUrl' => $data['SEFUrl'], 'Criteria' => $data['Criteria']];
            }

            return $collection;
        } catch (\Exception $e) {
            $this->_helper->fitmentLog('getCachedQualifiers Error:' . $e);
        }

        return $collection;
    }

    public function cachePartNumbers() {
        try {
            $partnumbers = $this->_fitment->getPartNumbers();
            $fitmentID = $partnumbers['YearID'] . $partnumbers['MakeID'] . $partnumbers['ModelID'] . $partnumbers['SubModelID'];
            $partnumbers['FitmentID'] = (int) $fitmentID;

            if (!$this->isPartNumbersCached($fitmentID)) {
                $partnumbers['PartNumbers'] = json_encode($partnumbers['PartNumbers']);
                $partnumbers['id'] = null;


                $model = $this->_partNumbersCache->create();
                $model->setData($partnumbers)->save();

                $this->_helper->fitmentLog('Successfully cached Fitment PartNumbers: ' . $partnumbers['PartNumbers']);
            }
        } catch (\Exception $e) {
            $this->_helper->fitmentLog('Error caching Fitment PartNumbers:' . $e);
        }
    }

    /**
     * check if Fitment PartNumbers is already cached
     * @param string $fitmentID
     * @return boolean
     */
    private function isPartNumbersCached($fitmentID) {
        try {
            $partnumbers = $this->_partNumbersCache->create();
            $collection = $partnumbers->getCollection();
            $collection->addFieldToFilter('FitmentID', ['eq' => trim($fitmentID)]);

            return $collection->count() > 0 ? TRUE : FALSE;
        } catch (\Exception $e) {
            $this->_helper->fitmentLog('isPartNumberCached Error:' . $e);
        }

        return FALSE;
    }

    /**
     * @param array $params
     * @return object
     */
    private function getCachedPartNumbersCollection($params = []) {
        try {
            $year = isset($params['YearID']) ? $params['YearID'] : $this->_fitment->_year;
            $make = isset($params['MakeID']) ? $params['MakeID'] : $this->_fitment->_make;
            $model = isset($params['ModelID']) ? $params['ModelID'] : $this->_fitment->_model;
            $submodel = isset($params['SubModelID']) ? $params['SubModelID'] : $this->_fitment->_submodel;
            $qualifiers = isset($params['Qualifiers']) ? $params['Qualifiers'] : $this->_fitment->_qualifier;

            $partnumber = $this->_partNumbersCache->create();
            $collection = $partnumber->getCollection();
            $collection->addFieldToFilter('YearID', ['eq' => $year]);
            $collection->addFieldToFilter('MakeID', ['eq' => $make]);
            $collection->addFieldToFilter('ModelID', ['eq' => $model]);
            $collection->addFieldToFilter('SubModelID', ['eq' => $submodel]);
            /**
             * Todo: add qualifiers filter here
             */
//            echo $collection->getSelect()->__toString();

            return $collection;
        } catch (\Exception $e) {
            $this->_helper->fitmentLog('getCachedFitmentCollection Error:' . $e);
        }
    }

    /**
     * 
     * @param array $params
     * @return array
     */
    public function getCachedPartNumbers($params = []) {
        $collection = [];

        try {
            $params['YearID'] = isset($params['YearID']) ? $params['YearID'] : $this->_fitment->_year;
            $params['MakeID'] = isset($params['MakeID']) ? $params['MakeID'] : $this->_fitment->_make;
            $params['ModelID'] = isset($params['ModelID']) ? $params['ModelID'] : $this->_fitment->_model;
            $params['SubModelID'] = isset($params['SubModelID']) ? $params['SubModelID'] : $this->_fitment->_submodel;

            $partnumbers = $this->getCachedPartNumbersCollection($params);

            foreach ($partnumbers as $key => $partnumber) {
                $data = $partnumber->getData();
                $collection[$data['FitmentID']] = [
                    'FitmentID' => $data['FitmentID'],
                    'YearID' => $data['YearID'],
                    'MakeID' => $data['MakeID'],
                    'ModelID' => $data['ModelID'],
                    'SubModelID' => $data['SubModelID'],
                    'Qualifiers' => $data['Qualifiers'],
                    'PartNumbers' => $data['PartNumbers']
                ];
            }

            return $collection;
        } catch (\Exception $e) {
            $this->_helper->fitmentLog('getCachedPartNumbers Error:' . $e);
        }

        return $collection;
    }

    public function getCachedPartNumbers2($params = []) {
        $collection = [];

        try {
            $params['YearID'] = isset($params['YearID']) ? $params['YearID'] : $this->_fitment->_year;
            $params['MakeID'] = isset($params['MakeID']) ? $params['MakeID'] : $this->_fitment->_make;
            $params['ModelID'] = isset($params['ModelID']) ? $params['ModelID'] : $this->_fitment->_model;
            $params['SubModelID'] = isset($params['SubModelID']) ? $params['SubModelID'] : $this->_fitment->_submodel;
            $partnumbers = $this->getCachedPartNumbersCollection($params);

            foreach ($partnumbers as $key => $partnumber) {
                $data = $partnumber->getData();
                $collection[$data['PartNumber']] = ['YearID' => $data['YearID'], 'MakeID' => $data['MakeID'], 'ModelID' => $data['ModelID'], 'SubModelID' => $data['SubModelID'], 'PartNumber' => $data['PartNumber']];
            }

            return $collection;
        } catch (\Exception $e) {
            $this->_helper->fitmentLog('getCachedPartNumbers Error:' . $e);
        }
    }

}
