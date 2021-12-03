<?php

namespace AJH\Fitment\Model\Fitment;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\App\RequestInterface;

class Cache {

    protected $_resource;
    protected $_fitmentApi;
    protected $_request;
    protected $_fitmentMakes;

    public function __construct(ResourceConnection $resource,
            RequestInterface $request) {
        $this->_resource = $resource;
        $this->_request = $request;
    }

    public function cacheFitmentYears($years) {
        $cached_years = $this->getCachedFitmentYears();

        if (count($cached_years) < 1) {
            $resource = $this->_resource;
            $connW = $resource->getConnection('core_write');

            $query = "INSERT INTO `api_fitment_years` (`year`) "
                    . "VALUES ";

            $key = 0;
            foreach ($years as $year) {
                if ($key > 0 && $key < count($years)) {
                    $query .= ",";
                }
                $query .= "('{$year}')";
                $key++;
            }

            $connW->query($query);
        }
    }

    public function cacheFitmentMakes($makes) {
        $year = $this->_request->getParam("year");

        $cached_makes = $this->getCachedFitmentMakes($year);

        if (count($cached_makes) < 1) {
            $resource = $this->_resource;
            $connW = $resource->getConnection('core_write');

            $query = "INSERT INTO `api_fitment_makes` (`year`, `makeID`, `makeName`, `makeLogo`, `make`) "
                    . "VALUES ";

            $key = 0;
            foreach ($makes as $make) {
                if ($key > 0 && $key < count($makes)) {
                    $query .= ",";
                }
                $query .= "('{$year}','{$make->MakeID}','{$make->MakeName}','{$make->LOGO_URL}','" . json_encode($make) . "')";
                $key++;
            }

            $connW->query($query);
        }
    }

    public function cacheFitmentModels($models) {
        $year = $this->_request->getParam("year");
        $make = $this->_request->getParam("make");

        $cached_models = $this->getCachedFitmentModels($year, $make);

        if (count($cached_models) < 1) {
            $resource = $this->_resource;
            $connW = $resource->getConnection('core_write');

            $query = "INSERT INTO `api_fitment_models` (`year`, `makeID`, `modelID`, `modelName`, `model`) "
                    . "VALUES ";
            $key = 0;
            $modelId = 0;
            if (is_array($models)) {
                foreach ($models as $model) {
                    if ($model->ModelID) {
                        $modelId = $model->ModelID;
                        if ($key > 0 && $key < count($models)) {
                            $query .= ",";
                        }
                        $query .= "('{$year}', '{$make}','{$model->ModelID}','{$model->ModelName}','" . json_encode($model) . "')";
                        $key++;
                    }
                }
            } else {
                if ($models->ModelID) {
                    $modelId = $models->ModelID;
                }
                $query .= "('{$year}', '{$make}','{$models->ModelID}','{$models->ModelName}','" . json_encode($models) . "')";
            }

            if ($modelId) {
                $connW->query($query);
            }
        }
    }

    public function cacheFitmentSubModels($submodels) {
        $year = $this->_request->getParam("year");
        $make = $this->_request->getParam("make");
        $model = $this->_request->getParam("model");

        $cached_submodels = $this->getCachedFitmentSubModels($year, $make, $model);


        if (count($cached_submodels) < 1) {
            $resource = $this->_resource;
            $connW = $resource->getConnection('core_write');

            $query = "INSERT INTO `api_fitment_submodels` (`year`, `makeID`, `modelID`, `submodelID`, `submodelName`, `submodel`) "
                    . "VALUES ";

            $key = 0;
            $submodelId = 0;
            if (is_array($submodels)) {
                foreach ($submodels as $submodel) {
                    if ($submodel->SubModelID) {
                        $submodelId = $submodel->SubModelID;
                        if ($key > 0 && $key < count($submodels)) {
                            $query .= ",";
                        }
                        $query .= "('{$year}', '{$make}','{$model}','{$submodel->SubModelID}','{$submodel->SubModelName}','" . json_encode($submodel) . "')";
                        $key++;
                    }
                }
            } else {
                if ($submodels->SubModelID) {
                    $submodelId = $submodels->SubModelID;
                }
                $query .= "('{$year}', '{$make}','{$model}','{$submodels->SubModelID}','{$submodels->SubModelName}','" . json_encode($submodels) . "')";
            }

            if ($submodelId) {
                $connW->query($query);
            }
        }
    }

    public function cacheFitmentQualifiers($qualifiers) {
        $year = $this->_request->getParam("year");
        $make = $this->_request->getParam("make");
        $model = $this->_request->getParam("model");
        $submodel = $this->_request->getParam("submodel");

        $cached_qualifiers = $this->getCachedFitmentQualifiers($year, $make, $model, $submodel);

        if (count($cached_qualifiers) < 1) {
            $resource = $this->_resource;
            $connW = $resource->getConnection('core_write');
            $query = "INSERT INTO `api_fitment_qualifiers` (`year`, `makeID`, `modelID`, `submodelID`, `qualifiers`) "
                    . "VALUES ";
            $query .= "('{$year}', '{$make}','{$model}','{$submodel}','" . json_encode($qualifiers) . "')";
            $connW->query($query);
        }
    }

    private function getCachedFitmentYears() {
        $resource = $this->_resource;
        $readConnection = $resource->getConnection('core_read');
        $query = 'SELECT * FROM ' . $resource->getTableName('api_fitment_years');

        $years = $readConnection->fetchAll($query);

        $_years = array();
        foreach ($years as $year) {
            array_push($_years, $year['year']);
        }

        return $_years;
    }

    public function getCachedFitmentMakes($year) {
        $resource = $this->_resource;
        $readConnection = $resource->getConnection('core_read');
        $query = 'SELECT * FROM ' . $resource->getTableName('api_fitment_makes') . ' WHERE `year` = "' . $year . '"';

        $makes = $readConnection->fetchAll($query);

        $_makes = array();
        foreach ($makes as $make) {
            array_push($_makes, (object) array('MakeID' => $make['makeID'], 'MakeName' => $make['makeName'], 'LOGO_URL' => $make['makeLogo']));
        }

        return $_makes;
    }

    public function _getCachedFitmentMakes() {
        $_makes = [];
        $years_arr = [];
        $years = $this->getCachedFitmentYears();

        foreach ($years as $year) {
            $makes = $this->getCachedFitmentMakes($year);
            foreach ($makes as $make) {
                $years_arr[$make->MakeID]['year'][] = ['year' => $year, 'image' => $make->LOGO_URL, 'make' => $make->MakeID];
                $_makes[$make->MakeID] = array('MakeYear' => $years_arr[$make->MakeID]['year'], 'MakeName' => $make->MakeName, 'MakeID' => $make->MakeID, 'LOGO_URL' => $make->LOGO_URL);
                $_makes[$make->MakeID]['years'] = $years_arr[$make->MakeID]['year'];
            }
        }

        return $_makes;
    }

    public function getCachedFitmentModels($year, $make) {
        $resource = $this->_resource;
        $readConnection = $resource->getConnection('core_read');
        $query = 'SELECT * FROM ' . $resource->getTableName('api_fitment_models') . ' WHERE `year` = "' . $year . '" AND `makeID` = "' . $make . '"';

        $models = $readConnection->fetchAll($query);

        $_models = array();
        foreach ($models as $model) {
            array_push($_models, (object) array('ModelID' => $model['modelID'], 'ModelName' => $model['modelName']));
        }

        return $_models;
    }

    public function getCachedFitmentSubModels($year, $make, $model) {
        $resource = $this->_resource; //Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $query = 'SELECT * FROM ' . $resource->getTableName('api_fitment_submodels') . ' WHERE `year` = "' . $year . '" AND `makeID` = "' . $make . '" AND `modelID` = "' . $model . '"';

        $submodels = $readConnection->fetchAll($query);

        $_submodels = array();
        foreach ($submodels as $submodel) {
            array_push($_submodels, (object) array('SubModelID' => $submodel['submodelID'], 'SubModelName' => $submodel['submodelName']));
        }

        return $_submodels;
    }

    private function getCachedFitmentQualifiers($year, $make, $model, $submodel) {
        $resource = $this->_resource;
        $readConnection = $resource->getConnection('core_read');
        $query = 'SELECT `qualifiers` FROM ' . $resource->getTableName('api_fitment_qualifiers') . ' WHERE `year` = "' . $year . '" AND `makeID` = "' . $make . '" AND `modelID` = "' . $model . '" AND `submodelID` = "' . $submodel . '"';

        $qualifiers = $readConnection->fetchOne($query);

        return count($qualifiers) ? json_decode($qualifiers) : [];
    }

    public function cacheFitment($fitment) {
        $resource = $this->_resource;
        $connW = $resource->getConnection('core_write');

        if (intval($fitment['year']) !== 0 && intval($fitment['makeID']) !== 0) {
            $query = "INSERT INTO `api_fitment` (`year`,`makeID`, `make`, `modelID`, `model`, `submodelID`, `submodel`, `qualifiers`, `skus`) "
                    . "VALUES "
                    . "('{$fitment['year']}', '{$fitment['makeID']}', '{$fitment['make']}', '{$fitment['modelID']}', '{$fitment['model']}', '{$fitment['submodelID']}', '{$fitment['submodel']}', '{$fitment['qualifiers']}', '{$fitment['skus']}')";

            $connW->query($query);
        }
    }

    public function getCachedFitment($fitment) {
        $resource = $this->_resource;
        $readConnection = $resource->getConnection('core_read');
        $query = 'SELECT `skus` FROM ' . $resource->getTableName('api_fitment') . ' WHERE `year` = "' . $fitment['year'] . '" AND `makeID` = "' . $fitment['makeID'] . '" AND `modelID` = "' . $fitment['modelID'] . '" AND `submodelID` = "' . $fitment['submodelID'] . '" AND `qualifiers` = "' . $fitment['qualifiers'] . '" ORDER BY `id` DESC';
        $skus = $readConnection->fetchAll($query);

//        $skus_arr = unserialize($skus[0]['skus']);
//        $storeId = Mage::app()->getStore()->getId();

        if (count($skus)) {
            return unserialize($skus[0]['skus']);
        } else {
            return null;
        }
    }

}
