<?php

namespace AJH\Fitment\Model\Fitment;

use AJH\Fitment\Model\Fitment\Vehicles as FitmentVehicles;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use AJH\Fitment\Helper\Fitment as FitmentHelper;
use Magento\Framework\App\Request\Http as HttpRequest;
use AJH\Fitment\Helper\Data as FitmentHelperData;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\App\ResourceConnection;
use AJH\Fitment\Model\Fitment\Cache as FitmentCache;

class Api {

    protected $_makes = array();
    public $_fitments = array();
    public $_partByVehicleIDs = array();
    protected $_client = null;
    private $_params = array();
    protected $_skus = [];
    protected $_year = 0;
    public $_makes2 = array();
    protected $_makeID = 0;
    protected $_models = array();
    protected $_modelID = 0;
    protected $_submodels = array();
    protected $_submodelID = 0;
    private $_skus_no_qualifiers = array();
    protected $_qualifiers = array();
    protected $_resource;
    protected $_request, $_productCollectionFactory;
    protected $_fitmentVehicle;
    protected $_storeManager, $_scopeConfig, $_fitmentHelper;
    private $fitmentCache;

    public function __construct(Context $context, Registry $registry,
            FitmentHelperData $data, ResourceConnection $resource,
            ProductFactory $productCollectionFactory,
            FitmentVehicles $fitmentVehicle,
            StoreManagerInterface $storeManager,
            ScopeConfigInterface $scopeConfig, FitmentHelper $fitmentHelper,
            HttpRequest $request, FitmentCache $fitmentCache
    ) {

        $this->_resource = $resource;
        $this->_request = $request;
        $this->helper = $data;
        $this->_client = $this->helper->_client;
        $this->_params = $this->helper->_params;

        $this->_productCollectionFactory = $productCollectionFactory;

        $this->_fitmentVehicle = $fitmentVehicle;
        $this->_storeManager = $storeManager;
        $this->_scopeConfig = $scopeConfig;
        $this->_fitmentHelper = $fitmentHelper;

        $this->fitmentCache = $fitmentCache;
    }

    public function getYears() {

        $fitmentYears = $this->_client->FitmentYears($this->_params)->FitmentYearsResult->FitmentYears->int;

        $this->fitmentCache->cacheFitmentYears($fitmentYears);        

        return $fitmentYears;
    }

    public function getMakesByYear($year) {
        $this->_makes = null;

        if ($year) {
            $this->_params['FitmentYear'] = $year;
            $this->_makes = $this->_client->FitmentMakes($this->_params)->FitmentMakesResult->FitmentMakes->FitmentMake;
        }

        return $this->_makes;
    }

    public function getMakes() {
        $year = $this->_request->getParam("year");
        $makes = [];
        $_makes = [];

        if ($year) {
            $this->_params['FitmentYear'] = $year;
            $makes = $this->_client->FitmentMakes($this->_params)->FitmentMakesResult->FitmentMakes->FitmentMake;

            $this->fitmentCache->cacheFitmentMakes($makes);

            foreach ($makes as $_make) {
                $make = [];
                $make['ID'] = $_make->MakeID;
                $make['Name'] = $_make->MakeName;
                $make['Logo'] = $_make->LOGO_URL;

                $_makes[] = $make;
            }
        }

        return $_makes;
    }

    public function getMake($makeID) {
        $make = [];
        $year = $this->_request->getParam("year");

        if ($year) {
            $makes = $this->getMakes();

            foreach ($makes as $_make) {
                if ((int) $makeID === (int) $_make['ID']) {
                    return $_make;
                }
            }
        }

        return $make;
    }

    /*
     * updated: 042021
     */

    public function getModels($year = null, $make = null) {
        if (!$year) {
            $year = $this->_request->getParam('year');
        }

        if (!$make) {
            $make = $this->_request->getParam('make');
        }

        $models = [];
        $_models = [];


        if ($year && $make) {
            $this->_params['FitmentYear'] = $year;
            $this->_params['FitmentMakeID'] = $make;

            $models = $this->_client->FitmentModels($this->_params)->FitmentModelsResult->FitmentModels->FitmentModel;

            $this->fitmentCache->cacheFitmentModels($models);

            if (is_array($models)) {
                foreach ($models as $_model) {
                    $model = [];
                    $model['ID'] = $_model->ModelID;
                    $model['Name'] = $_model->ModelName;

                    $_models[] = $model;
                }
            } else {
                $model = [];
                $model['ID'] = $models->ModelID;
                $model['Name'] = $models->ModelName;

                $_models[] = $model;
            }
        }

        return $_models;
    }

    /*
     * added 4/22/21
     */

    public function getModelsByYearMake($year, $make) {
        if (!$year) {
            $year = $this->_request->getParam('year');
        }

        if (!$make) {
            $make = $this->_request->getParam('make');
        }

        $models = [];

        if ($year && $make) {
            $this->_params['FitmentYear'] = $year;
            $this->_params['FitmentMakeID'] = $make;

            $models = $this->_client->FitmentModels($this->_params)->FitmentModelsResult->FitmentModels->FitmentModel;
        }

        return $models;
    }

    public function getModel($modelID) {
        $model = [];
        $year = $this->_request->getParam("year");
        $make = $this->_request->getParam("make");

        if ($year && $make) {

            $models = $this->getModels();

            foreach ($models as $_model) {
                if ((int) $modelID === (int) $_model['ID']) {
                    return $_model;
                }
            }
        }

        return $model;
    }

    public function getSubModel($submodelID) {
        $submodel = [];
        $year = $this->_request->getParam("year");
        $make = $this->_request->getParam("make");
        $model = $this->_request->getParam("model");

        if ($year && $make && $model) {
            $submodels = $this->getSubModels();

            foreach ($submodels as $_submodel) {
                if ((int) $submodelID === (int) $_submodel->SubModelID) {
                    $submodel['ID'] = $_submodel->SubModelID;
                    $submodel['Name'] = $_submodel->SubModelName;
                }
            }
        }

        return $submodel;
    }

    public function getSubModels() {
        $year = $this->_request->getParam('year');
        $make = $this->_request->getParam('make');
        $model = $this->_request->getParam("model");

        $submodels = [];
        $_submodels = [];

        if ($year && $make && $model) {
            $this->_params['FitmentYear'] = $year;
            $this->_params['FitmentMakeID'] = $make;
            $this->_params['FitmentModelID'] = $model;

            $submodels = $this->_client->FitmentSubModels($this->_params)->FitmentSubModelsResult->FitmentSubModels->FitmentSubModel;

            $this->fitmentCache->cacheFitmentSubModels($submodels);

            if (is_array($submodels)) {
                foreach ($submodels as $_submodel) {
                    $submodel = [];
                    $submodel['ID'] = $_submodel->SubModelID;
                    $submodel['Name'] = $_submodel->SubModelName;

                    $_submodels[] = $submodel;
                }
            } else {
                $submodel = [];
                $submodel['ID'] = $submodels->SubModelID;
                $submodel['Name'] = $submodels->SubModelName;

                $_submodels[] = $submodel;
            }
        }

        return $_submodels;
    }

    /*
     * added 4/22/21
     */

    public function getSubModelsByYearMakeModel($year, $make, $model) {
        if (!$year) {
            $year = $this->_request->getParam('year');
        }

        if (!$make) {
            $make = $this->_request->getParam('make');
        }
        if (!$model) {
            $model = $this->_request->getParam('model');
        }

        $submodels = [];

        if ($year && $make && $model) {
            $this->_params['FitmentYear'] = $year;
            $this->_params['FitmentMakeID'] = $make;
            $this->_params['FitmentModelID'] = $model;

            $submodels = $this->_client->FitmentSubModels($this->_params)->FitmentSubModelsResult->FitmentSubModels->FitmentSubModel;
        }

        return $submodels;
    }

    public function getQualifiers() {
        $year = $this->_request->getParam('year');
        $make = $this->_request->getParam('make');
        $model = $this->_request->getParam("model");
        $submodel = $this->_request->getParam("submodel");

        $qualifiers = [];
        $_qualifiers = [];

        if ($year && $make && $model && $submodel) {
            $this->_params['FitmentYear'] = $year;
            $this->_params['FitmentMakeID'] = $make;
            $this->_params['FitmentModelID'] = $model;
            $this->_params['FitmentSubModelID'] = $submodel;

            if (property_exists($this->_client, 'FitmentQualifiers') && $qualifiers = $this->_client->FitmentQualifiers($this->_params)->FitmentQualifiersResult->Qualifiers->FitmentQualifier) {

                if (is_array($qualifiers)) {
                    foreach ($qualifiers as $_qualifier) {
                        $qualifier = [];
                        $qualifier['Description'] = $_qualifier->QualifierDescription;
                        $qualifier['FitmentValue'] = $_qualifier->QualifierValues->FitmentValue;

                        $_qualifiers[] = $qualifier;
                    }
                } else {
                    $qualifier = [];
                    $qualifier['Description'] = $qualifiers->QualifierDescription;
                    $qualifier['FitmentValue'] = $qualifiers->QualifierValues->FitmentValue;

                    $_qualifiers[] = $qualifier;
                }

                $this->fitmentCache->cacheFitmentQualifiers($_qualifiers);
            }
        }

        return $_qualifiers;
    }

    //    public function fitmentQualifiers($year, $make, $model, $submodel) {
//        $qualifiers = $this->getCachedFitmentQualifiers($year, $make, $model, $submodel);
//
//        if (!count($qualifiers)) {
//            $this->_params['FitmentYear'] = $year;
//            $this->_params['FitmentMakeID'] = $make;
//            $this->_params['FitmentModelID'] = $model;
//            $this->_params['FitmentSubModelID'] = $submodel;
//
//            $this->_submodelID = $submodel;
//
//            $this->_skus_no_qualifiers = $this->retrieveVehicleParts($year, $make, $model, $submodel, null, null);
//            $qualifiers = $this->_client->FitmentQualifiers($this->_params)->FitmentQualifiersResult->Qualifiers;
//
//            $this->cacheFitmentQualifiers($year, $make, $model, $submodel, $qualifiers);
//        }
//
//        return $qualifiers;
//    }
//    private function cacheFitmentMakes($year, $makes) {
//        $cached_makes = $this->getCachedFitmentMakes($year);
//
//
//        if (!$cached_makes) {
//            $resource = $this->_resource; //Mage::getSingleton('core/resource');
//            $connW = $resource->getConnection('core_write');
//
//            $query = "INSERT INTO `api_fitment_makes` (`year`, `makeID`, `makeName`, `makeLogo`, `make`) "
//                    . "VALUES ";
//
//            $key = 0;
//            foreach ($makes as $make) {
//                if ($key > 0 && $key < count($makes)) {
//                    $query .= ",";
//                }
//                $query .= "('{$year}','{$make->MakeID}','{$make->MakeName}','{$make->LOGO_URL}','" . json_encode($make) . "')";
//                $key++;
//            }
//
//            $connW->query($query);
//        }
//    }
//    public function retrievePartByVehicleID($baseVehicleIds) {
//        $this->_params['IDType'] = 'V';
//        $this->_params['VehicleID'] = 39246; //hard-coded for test only
//
//        array_push($this->_partByVehicleIDs, $this->_client->RetrievePartByVehicleID_V2($this->_params)->RetrievePartByVehicleID_V2Result);
//
//        return $this->_partByVehicleIDs;
//    }
//    public function retrieveVehicleParts($yearID, $makeID, $modelID, $submodelID, $qualifiers, $_qualifiers) {
//        $fitmentValues = '';
//        $qualifiers_arr = [];
//        $_qualifiers_arr = [];
//        $_qualifiers = [];
//
//        if (isset($qualifiers) && is_array($qualifiers)) {
//            foreach ($qualifiers as $key => $qualifier) {
//                if ($key == 0) {
//                    $qualifiers_arr = explode(",", $qualifier);
//                }
//            }
//        }
//
//        if (isset($_qualifiers) && is_array($_qualifiers)) {
//            foreach ($_qualifiers as $key => $_qualifier) {
//                if ($key == 0) {
//                    $_qualifiers_arr = explode(",", $_qualifier);
//                }
//            }
//        }
//
//
//        if (count($qualifiers_arr)) {
//
//            $_qualifiers_str = array();
//            foreach ($qualifiers_arr as $key => $qualifier) {
//                if ($qualifier !== '' && count($_qualifiers_arr)) {
//                    array_push($_qualifiers_str, $_qualifiers_arr[$key] . '=' . $qualifier);
//                }
//            }
//
//            $fitmentValues = implode("|", $_qualifiers_str);
//        }
//
//        $this->_params['FitmentYear'] = $yearID;
//        $this->_params['FitmentMakeID'] = $makeID;
//        $this->_params['FitmentModelID'] = $modelID;
//        $this->_params['FitmentSubModelID'] = $submodelID;
//        $this->_params['FitmentQualifiers'] = $fitmentValues;
//
//        $fitment = array(
//            'year' => $yearID,
//            'makeID' => $makeID,
//            'make' => json_encode($this->getFitmentMakeNameByID($yearID, $makeID)),
//            'modelID' => $modelID,
//            'model' => json_encode($this->getFitmentModelNameByID($yearID, $makeID, $modelID)),
//            'submodelID' => $submodelID,
//            'submodel' => json_encode($this->getFitmentSubModelNameByID($yearID, $makeID, $modelID, $submodelID)),
//            'qualifiers' => $fitmentValues
//        );
//
//        $_skus = $this->getCachedFitment($fitment);
//
//        if (is_null($_skus)) {
//            $_skus = array();
//
//            if ($yearID) {
//                $parts = $this->_client->RetrieveVehicleParts($this->_params)->RetrieveVehiclePartsResult->Parts->FitmentPart;
//            } else {
//                $parts = null;
//            }
//
//            foreach ($parts as $part) {
//                if (!in_array($part->PartNumber, $_skus)) {
//                    array_push($_skus, $part->PartNumber);
//                }
//            }
//
//            $fitment['skus'] = serialize($_skus);
//            $this->cacheFitment($fitment);
//        }
//
//        return $_skus;
//    }
//    public function _retrieveVehicleParts($yearID, $makeID, $modelID, $submodelID, $qualifiers) {
//        if (count($qualifiers) && $yearID) {
//
//            foreach ($qualifiers as $qualifier) {
//                $fitmentValues[] = array(
//                    'vals' => array(
//                        'FitmentValue' => array(
//                            'ID' => $qualifier->ID,
//                            'Name' => $qualifier->Name
//                        )
//                    )
//                );
//            }
//        } else {
//            $fitmentValues = array();
//        }
//
//        $this->_params['FitmentYear'] = $yearID;
//        $this->_params['FitmentMakeID'] = $makeID;
//        $this->_params['FitmentModelID'] = $modelID;
//        $this->_params['FitmentSubModelID'] = $submodelID;
//        $this->_params['FitmentValues'] = $fitmentValues; //(count($qualifiers) < 2 || !is_array($qualifiers)) && $fitmentValue[0]['ID'] != null ? array('vals' => array('FitmentValue' => $fitmentValue[0])) : (count($qualifiers) >= 2 ? array('vals' => array('FitmentValue' => $fitmentValue[0], 'FitmentValue' => $fitmentValue[1])) : null );
//
//        $parts = $this->_client->RetrieveVehicleParts($this->_params)->RetrieveVehiclePartsResult->Parts->FitmentPart;
//        return $parts;
//    }

    public function _retrieveVehicleQParts($params) {
        $parts = $this->_client->RetrieveVehicleParts($params)->RetrieveVehiclePartsResult->Parts->FitmentPart;

        return $this->cleanDuplicateSkus($parts);
    }

    public function getProductCollection() {
        $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        return $collection;
    }

//
//    public function getVehicleParts() {
//        $catId = $this->_request->getParam('cat');
//
//        $this->_skus = $this->retrieveVehicleParts(
//                $this->getFitmentYearID(), $this->getFitmentMakeID(), $this->getFitmentModelID(), $this->getFitmentSubModelID(), $this->getFitmentQualifiers(), $this->getFitmentQualifiers2()
//        );
//
//        $params = $this->_request->getParams();
//
//        $collection = $this->_productCollectionFactory->create()
//                ->addAttributeToSelect('*')
//                ->addAttributeToFilter('status', 1)
//                ->joinField('category_id', 'catalog_category_product', 'category_id', 'product_id=entity_id', null, 'left');
////                ->addAttributeToFilter('category_id', array('in' => $catId))
////                ->addAttributeToFilter('sku', array('in' => $this->_skus))
////                ->addFieldToFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH);                
////        foreach ($params as $code => $param) {
////            $entity = 'catalog_product';
////            $attr = $this->_eavConfig->getAttribute($entity, $code);
////            
////
////            if ($attr->getId()) {
////                echo $code;
////                $collection->addAttributeToFilter($code, array('eq' => $param));
////            }
////        }
//
//        $collection->load();
//
//        die('end');
//
//
//        return $collection;
//    }
//    public function loadFitmentSkus() {
//        $this->_skus = $this->retrieveVehicleParts(
//                $this->getFitmentYearID(), $this->getFitmentMakeID(), $this->getFitmentModelID(), $this->getFitmentSubModelID(), $this->getFitmentQualifiers(), $this->getFitmentQualifiers2()
//        );
//
//        return $this->_skus;
//    }
//    public function loadAllFitment() {
//        $years = $this->fitmentYears();
//
//        echo '<table border="1" width="100%">';
//        foreach ($years as $year) {
//            $makes = $this->fitmentMakes($year);
//            foreach ($makes as $make) {
//                if ($make->MakeID) {
//                    $models = $this->fitmentModels($year, $make->MakeID);
//                    foreach ($models as $model) {
//                        if ($model->ModelID) {
//                            $submodels = $this->fitmentSubModels($year, $make->MakeID, $model->ModelID);
//                            foreach ($submodels as $submodel) {
//                                if ($submodel->SubModelID) {
//                                    $qualifiers = $this->fitmentQualifiers($year, $make->MakeID, $model->ModelID, $submodel->SubModelID);
//
//                                    if (count($qualifiers->FitmentQualifier)) {
//
//                                        if (is_array($qualifiers->FitmentQualifier)) {
//                                            foreach ($qualifiers->FitmentQualifier[0]->QualifierValues->FitmentValue as $_fqualifier) {
//                                                if (count($qualifiers->FitmentQualifier[1])) {
//                                                    foreach ($qualifiers->FitmentQualifier[1]->QualifierValues->FitmentValue as $fqualifier) {
//                                                        $parts = $this->_retrieveVehicleParts($year, $make->MakeID, $model->ModelID, $submodel->SubModelID, array($_fqualifier, $fqualifier));
//                                                        $displayedParts = array();
//                                                        echo '<tr>';
//                                                        echo '<td>';
//                                                        echo $year;
//                                                        echo '</td>';
//                                                        echo '<td>';
//                                                        echo $make->MakeName;
//                                                        echo '</td>';
//                                                        echo '<td>';
//                                                        echo $model->ModelName;
//                                                        echo '</td>';
//                                                        echo '<td>';
//                                                        echo $submodel->SubModelName;
//                                                        echo '</td>';
//                                                        echo '<td>';
//                                                        echo $qualifiers->FitmentQualifier[0]->QualifierDescription . '<br/>';
//                                                        echo $qualifiers->FitmentQualifier[1]->QualifierDescription;
//                                                        echo '</td>';
//                                                        echo '<td>';
//                                                        echo $_fqualifier->Name . '<br>';
//                                                        echo $fqualifier->Name;
//                                                        echo '</td>';
//                                                        echo '<td>';
//
//                                                        foreach ($parts as $key => $part) {
//                                                            if (!in_array($part->PartNumber, $displayedParts)) {
//                                                                echo ($key + 1) . '> ' . $part->PartNumber . '<br/> > ' . $part->PartDescription . '<br/>';
//                                                                echo '> ' . $part->PartClassDescription . '<br/> > ' . $part->PartSubClassDescription . '<br/><br/>';
//
//                                                                array_push($displayedParts, $part->PartNumber);
//                                                            }
//                                                        }
//
//                                                        echo '</td>';
//                                                        echo '</tr>';
//                                                    }
//                                                }
//                                            }
//                                        } else {
//                                            $parts = $this->_retrieveVehicleParts($year, $make->MakeID, $model->ModelID, $submodel->SubModelID, array($_fqualifier));
//                                            $displayedParts = array();
//                                            echo '<tr>';
//                                            echo '<td>';
//                                            echo $year;
//                                            echo '</td>';
//                                            echo '<td>';
//                                            echo $make->MakeName;
//                                            echo '</td>';
//                                            echo '<td>';
//                                            echo $model->ModelName;
//                                            echo '</td>';
//                                            echo '<td>';
//                                            echo $submodel->SubModelName;
//                                            echo '</td>';
//                                            echo '<td>';
//                                            echo $qualifiers->FitmentQualifier->QualifierDescription;
//                                            echo '</td>';
//                                            echo '<td>';
//                                            echo $_fqualifier->Name;
//                                            echo '</td>';
//                                            echo '<td>';
//
//                                            foreach ($parts as $key => $part) {
//                                                if (!in_array($part->PartNumber, $displayedParts)) {
//                                                    echo '> ' . $part->PartNumber . '<br/> > ' . $part->PartDescription . '<br/>';
//                                                    echo '> ' . $part->PartClassDescription . '<br/> > ' . $part->PartSubClassDescription . '<br/><br/>';
//
//                                                    array_push($displayedParts, $part->PartNumber);
//                                                }
//                                            }
//
//                                            echo '</td>';
//                                            echo '</tr>';
//                                        }
//                                    } else {
//                                        $parts = $this->_retrieveVehicleParts($year, $make->MakeID, $model->ModelID, $submodel->SubModelID, null);
//                                        $displayedParts = array();
//
//                                        echo '<tr>';
//                                        echo '<td>';
//                                        echo $year;
//                                        echo '</td>';
//                                        echo '<td>';
//                                        echo $make->MakeName;
//                                        echo '</td>';
//                                        echo '<td>';
//                                        echo $model->ModelName;
//                                        echo '</td>';
//                                        echo '<td>';
//                                        echo $submodel->SubModelName;
//                                        echo '</td>';
//                                        echo '<td>';
//                                        echo '</td>';
//                                        echo '<td>';
//                                        echo '</td>';
//                                        echo '<td>';
//                                        foreach ($parts as $key => $part) {
//                                            if (!in_array($part->PartNumber, $displayedParts)) {
//                                                echo '> ' . $part->PartNumber . '<br/> > ' . $part->PartDescription . '<br/>';
//                                                echo '> ' . $part->PartClassDescription . '<br/> > ' . $part->PartSubClassDescription . '<br/><br/>';
//
//                                                array_push($displayedParts, $part->PartNumber);
//                                            }
//                                        }
//                                        echo '</td>';
//                                        echo '</tr>';
//                                    }
//                                }
//                            }
//                        }
//                    }
//                }
//            }
//        }
//        echo '</table>';
//    }

    public function getFitmentYearID() {
        return $this->_request->getParam('year', false);
    }

    public function getFitmentMakeID() {
        return $this->_request->getParam('make', false);
    }

    public function getFitmentMakeNameByID($year, $makeID) {
        $make = array();

        $makes = $this->_fitmentMakes($year);

        foreach ($makes as $_make) {
            if (intval($_make->MakeID) === intval($makeID)) {
                $make = $_make;
                break;
            }
        }

        return $make;
    }

    public function getFitmentMakeName() {
        $make = array();
        $year = $this->getFitmentYearID();
        $makeID = $this->getFitmentMakeID();


        $makes = $this->fitmentMakes2($year);

        foreach ($makes as $_make) {

            if ($_make['MakeID'] == $makeID) {
                $make['ID'] = $_make['MakeID'];
                $make['Name'] = $_make['MakeName'];
            }
        }

        return $make;
    }

    public function getFitmentModelNameByID($year, $makeID, $modelID) {
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

    public function getFitmentModelName() {
        $model = array();
        $year = $this->getFitmentYearID();
        $makeID = $this->getFitmentMakeID();
        $modelID = $this->getFitmentModelID();

        $models = $this->fitmentModels($year, $makeID);


        foreach ($models as $_model) {
            if ($_model->ModelID == $modelID) {
                $model['ID'] = $_model->ModelID;
                $model['Name'] = $_model->ModelName;
            }
        }

        return $model;
    }

    public function getFitmentSubModelNameByID($year, $makeID, $modelID,
            $submodelID) {
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

    public function getFitmentSubModelName() {
        $submodel = array();
        $year = $this->getFitmentYearID();
        $makeID = $this->getFitmentMakeID();
        $modelID = $this->getFitmentModelID();
        $submodelID = $this->getFitmentSubModelID();

        $submodels = $this->fitmentSubModels($year, $makeID, $modelID);

        if (is_array($submodels)) {
            foreach ($submodels as $_submodel) {
                if ($_submodel->SubModelID == $submodelID) {
                    $submodel['ID'] = $_submodel->SubModelID;
                    $submodel['Name'] = $_submodel->SubModelName;
                }
            }
        } else {
            $submodel['ID'] = $submodels->SubModelID;
            $submodel['Name'] = $submodels->SubModelName;
        }

        return $submodel;
    }

    public function getFitmentModelID() {
        return $this->_request->getParam('model', false);
    }

    public function getFitmentSubModelID() {
        return $this->_request->getParam('submodel');
    }

//    public function getFitmentQualifiers() {
//        return $this->_request->getParam('qualifiers');
//    }

    public function getFitmentQualifiers2() {
        return $this->_request->getParam('_qualifiers');
    }

    //used in garage
    public function fitmentYears() {

        $fitmentYears = $this->getCachedFitmentYears();

        if (!count($fitmentYears)) {
            $fitmentYears = $this->_client->FitmentYears($this->_params)->FitmentYearsResult->FitmentYears->int;
            $this->cacheFitmentYears($fitmentYears);
        }

        return $fitmentYears;
    }

    public function fitmentYears2($makeID) {
        $years = $this->fitmentYears();
        $_years = array();

        foreach ($years as $key => $year) {
            if (intval($key) <= 20) {
                $_makes = $this->getCachedFitmentMakes($year);

                foreach ($_makes as $make) {
                    if ($this->hasMakeInYear($make, $makeID)) {
                        $_years[$year] = $this->setYearsModelImage($year, $make);
                    }
                }
            }
        }

        return $_years;
    }

    private function hasMakeInYear($make, $makeID) {

        if (intval($make->MakeID) === intval($makeID)) {
            return TRUE;
        }

        return FALSE;
    }

    private function setYearsModelImage($year, $make) {
        $_years = array();
        $image = null;


        $_years['year'] = $year;
        $_years['make'] = $make->MakeID;

        $models = $this->fitmentModels($year, $make->MakeID);

        foreach ($models as $model) {
            if (!isset($_years['image']) && method_exists($this->_fitmentVehicle, 'getVehicleImage')) {
                $image = $this->_fitmentVehicle->getVehicleImage($year, trim($make->MakeName), $model->ModelName);

                $_years['image'] = $this->setPlaceholderImage($image);
            }
        }

        return $_years;
    }

    public function setPlaceholderImage($image_url) {
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

    public function fitmentMakes2() {
        $makes = array();
        $years = $this->fitmentYears();

        foreach ($years as $year) {
            $_makes = $this->getCachedFitmentMakes($year);
            $makes = $this->getFitmentMakes2($_makes, $year);
        }

        return $makes;
    }

    public function getFitmentMakesList() {
        $list = array();
        $makes = $this->fitmentMakes2();

        foreach ($makes as $key => $make) {
            $list[$key]['MakeName'] = $make['MakeName'];
            $list[$key]['LOGO_URL'] = $make['LOGO_URL'];
            $list[$key]['MakeID'] = $make['MakeID'];
        }

        return $list;
    }

    private function getFitmentMakes2($makes, $year) {

        foreach ($makes as $make) {
            $this->_makes2[$make->MakeID]['year'][] = $year;
            $this->_makes2[$make->MakeID] = array('MakeYear' => $this->_makes2[$make->MakeID]['year'], 'MakeName' => $make->MakeName, 'MakeID' => $make->MakeID, 'LOGO_URL' => $make->LOGO_URL);
        }

        return $this->_makes2;
    }

//    private function cacheFitmentYears($years) {
//        $cached_years = $this->getCachedFitmentYears();
//        if (!$cached_years) {
//            $resource = $this->_resource; //Mage::getSingleton('core/resource');
//            $connW = $resource->getConnection('core_write');
//
//            $query = "INSERT INTO `api_fitment_years` (`year`) "
//                    . "VALUES ";
//
//            $key = 0;
//            foreach ($years as $year) {
//                if ($key > 0 && $key < count($years)) {
//                    $query .= ",";
//                }
//                $query .= "('{$year}')";
//                $key++;
//            }
//            $connW->query($query);
//        }
//    }
//
//    private function cacheFitmentMakes($year, $makes) {
//        $cached_makes = $this->getCachedFitmentMakes($year);
//
//
//        if (!$cached_makes) {
//            $resource = $this->_resource; //Mage::getSingleton('core/resource');
//            $connW = $resource->getConnection('core_write');
//
//            $query = "INSERT INTO `api_fitment_makes` (`year`, `makeID`, `makeName`, `makeLogo`, `make`) "
//                    . "VALUES ";
//
//            $key = 0;
//            foreach ($makes as $make) {
//                if ($key > 0 && $key < count($makes)) {
//                    $query .= ",";
//                }
//                $query .= "('{$year}','{$make->MakeID}','{$make->MakeName}','{$make->LOGO_URL}','" . json_encode($make) . "')";
//                $key++;
//            }
//
//            $connW->query($query);
//        }
//    }
//
//    private function cacheFitmentModels($year, $make, $models) {
//        $cached_models = $this->getCachedFitmentModels($year, $make);
//
//
//        if (!$cached_models) {
//            $resource = $this->_resource; //Mage::getSingleton('core/resource');
//            $connW = $resource->getConnection('core_write');
//
//            $query = "INSERT INTO `api_fitment_models` (`year`, `makeID`, `modelID`, `modelName`, `model`) "
//                    . "VALUES ";
//
//            $key = 0;
//            $modelId = 0;
//            if (is_array($models)) {
//                foreach ($models as $model) {
//                    if ($model->ModelID) {
//                        $modelId = $model->ModelID;
//                        if ($key > 0 && $key < count($models)) {
//                            $query .= ",";
//                        }
//                        $query .= "('{$year}', '{$make}','{$model->ModelID}','{$model->ModelName}','" . json_encode($model) . "')";
//                        $key++;
//                    }
//                }
//            } else {
//                if ($models->ModelID) {
//                    $modelId = $models->ModelID;
//                }
//                $query .= "('{$year}', '{$make}','{$models->ModelID}','{$models->ModelName}','" . json_encode($models) . "')";
//            }
//
//            if ($modelId) {
//                $connW->query($query);
//            }
//        }
//    }
//
//    private function cacheFitmentSubModels($year, $make, $model, $submodels) {
//        $cached_submodels = $this->getCachedFitmentSubModels($year, $make, $model);
//
//
//        if (!$cached_submodels) {
//            $resource = $this->_resource; //Mage::getSingleton('core/resource');
//            $connW = $resource->getConnection('core_write');
//
//            $query = "INSERT INTO `api_fitment_submodels` (`year`, `makeID`, `modelID`, `submodelID`, `submodelName`, `submodel`) "
//                    . "VALUES ";
//
//            $key = 0;
//            $submodelId = 0;
//            if (is_array($submodels)) {
//                foreach ($submodels as $submodel) {
//                    if ($submodel->SubModelID) {
//                        $submodelId = $submodel->SubModelID;
//                        if ($key > 0 && $key < count($submodels)) {
//                            $query .= ",";
//                        }
//                        $query .= "('{$year}', '{$make}','{$model}','{$submodel->SubModelID}','{$submodel->SubModelName}','" . json_encode($submodel) . "')";
//                        $key++;
//                    }
//                }
//            } else {
//                if ($submodels->SubModelID) {
//                    $submodelId = $submodels->SubModelID;
//                }
//                $query .= "('{$year}', '{$make}','{$model}','{$submodels->SubModelID}','{$submodels->SubModelName}','" . json_encode($submodels) . "')";
//            }
//
//            if ($submodelId) {
//                $connW->query($query);
//            }
//        }
//    }
//
//    private function cacheFitmentQualifiers($year, $make, $model, $submodel, $qualifiers) {
//        $cached_qualifiers = $this->getCachedFitmentQualifiers($year, $make, $model, $submodel);
//
//        if (!$cached_qualifiers) {
//            $resource = $this->_resource; //Mage::getSingleton('core/resource');
//            $connW = $resource->getConnection('core_write');
//            $query = "INSERT INTO `api_fitment_qualifiers` (`year`, `makeID`, `modelID`, `submodelID`, `qualifiers`) "
//                    . "VALUES ";
//            $query .= "('{$year}', '{$make}','{$model}','{$submodel}','" . serialize($qualifiers) . "')";
//            $connW->query($query);
//        }
//    }
//
    //used in Garage functions
    private function getCachedFitmentYears() {
        $resource = $this->_resource; // Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $query = 'SELECT * FROM ' . $resource->getTableName('api_fitment_years');

        $years = $readConnection->fetchAll($query);

        $_years = array();
        foreach ($years as $year) {
            array_push($_years, $year['year']);
        }

        return $_years;
    }

// used in Garage functions
    public function getCachedFitmentMakes($year) {
        $resource = $this->_resource; //Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $query = 'SELECT * FROM ' . $resource->getTableName('api_fitment_makes') . ' WHERE `year` = "' . $year . '"';

        $makes = $readConnection->fetchAll($query);

        $_makes = array();
        foreach ($makes as $make) {
            array_push($_makes, (object) array('MakeID' => $make['makeID'], 'MakeName' => $make['makeName'], 'LOGO_URL' => $make['makeLogo']));
        }

        return $_makes;
    }

    // used in Garage functions
    public function getCachedFitmentModels($year, $make) {
        $resource = $this->_resource; //Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $query = 'SELECT * FROM ' . $resource->getTableName('api_fitment_models') . ' WHERE `year` = "' . $year . '" AND `makeID` = "' . $make . '"';

        $models = $readConnection->fetchAll($query);

        $_models = array();
        foreach ($models as $model) {
            array_push($_models, (object) array('ModelID' => $model['modelID'], 'ModelName' => $model['modelName']));
        }

        return $_models;
    }

    // used in Garage functions
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

    // used in Garage functions
    private function getCachedFitmentQualifiers($year, $make, $model, $submodel) {
        $resource = $this->_resource; //Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $query = 'SELECT `qualifiers` FROM ' . $resource->getTableName('api_fitment_qualifiers') . ' WHERE `year` = "' . $year . '" AND `makeID` = "' . $make . '" AND `modelID` = "' . $model . '" AND `submodelID` = "' . $submodel . '"';

        $qualifiers = $readConnection->fetchAll($query);


        return count($qualifiers) ? unserialize($qualifiers[0]['qualifiers']) : null;
    }

//
//    private function cacheFitment($fitment) {
//        $resource = $this->_resource; //Mage::getSingleton('core/resource');
//        $connW = $resource->getConnection('core_write');
//
//        $query = "INSERT INTO `api_fitment` (`year`,`makeID`, `make`, `modelID`, `model`, `submodelID`, `submodel`, `qualifiers`, `skus`) "
//                . "VALUES "
//                . "('{$fitment['year']}', '{$fitment['makeID']}', '{$fitment['make']}', '{$fitment['modelID']}', '{$fitment['model']}', '{$fitment['submodelID']}', '{$fitment['submodel']}', '{$fitment['qualifiers']}', '{$fitment['skus']}')";
//
//        $connW->query($query);
//    }
//
//    private function getCachedFitment($fitment) {
//        $resource = $this->_resource; //Mage::getSingleton('core/resource');
//        $readConnection = $resource->getConnection('core_read');
//        $query = 'SELECT `skus` FROM ' . $resource->getTableName('api_fitment') . ' WHERE `year` = "' . $fitment['year'] . '" AND `makeID` = "' . $fitment['makeID'] . '" AND `modelID` = "' . $fitment['modelID'] . '" AND `submodelID` = "' . $fitment['submodelID'] . '" AND `qualifiers` = "' . $fitment['qualifiers'] . '" ORDER BY `id` DESC';
//        $skus = $readConnection->fetchAll($query);
//
////        $skus_arr = unserialize($skus[0]['skus']);
////        $storeId = Mage::app()->getStore()->getId();
//
//        if (count($skus)) {
//            return unserialize($skus[0]['skus']);
//        } else {
//            return null;
//        }
//    }

    public function _fitmentMakes($year) {
        $this->_year = $year;
        $this->_params['FitmentYear'] = $year;

        $this->_makes = $this->_client->FitmentMakes($this->_params)->FitmentMakesResult->FitmentMakes->FitmentMake;


        return $this->_makes;
    }

    //used in Garage functions
    public function fitmentModels($year, $make) {
        $this->_models = $this->getCachedFitmentModels($year, $make);
        if (!count($this->_models) && $year && $make) {
            $this->_params['FitmentYear'] = $year;
            $this->_params['FitmentMakeID'] = $make;
            $this->_makeID = $make;

            $this->_models = $this->_client->FitmentModels($this->_params)->FitmentModelsResult->FitmentModels->FitmentModel;
            $this->cacheFitmentModels($year, $make, $this->_models);
        }

        return $this->_models;
    }

    //used in Garage functions
    public function getFitmentModels() {
        $year = $this->_request->getParam('year');
        $make = $this->_request->getParam('make');

        $this->_models = $this->getCachedFitmentModels($year, $make);
        if (!count($this->_models) && $year && $make) {
            $this->_params['FitmentYear'] = $year;
            $this->_params['FitmentMakeID'] = $make;
            $this->_makeID = $make;

            $this->_models = $this->_client->FitmentModels($this->_params)->FitmentModelsResult->FitmentModels->FitmentModel;
            $this->cacheFitmentModels($year, $make, $this->_models);
        }

        return $this->_models;
    }

    //used in Garage functions
    public function fitmentSubModels($year, $make, $model) {
        $this->_submodels = $this->getCachedFitmentSubModels($year, $make, $model);
        if (!count($this->_submodels) && $year && $make && $model) {
            $this->_params['FitmentYear'] = $year;
            $this->_params['FitmentMakeID'] = $make;
            $this->_params['FitmentModelID'] = $model;

            $this->_modelID = $model;

            $this->_submodels = $this->_client->FitmentSubModels($this->_params)->FitmentSubModelsResult->FitmentSubModels->FitmentSubModel;
            $this->cacheFitmentSubModels($year, $make, $model, $this->_submodels);
        }

        return $this->_submodels;
    }

//    
//    public function getFitmentSubModels() {
//        $year = $this->_request->getParam('year');
//        $make = $this->_request->getParam('make');
//        $model = $this->_request->getParam('model');
//
//        $this->_submodels = $this->getCachedFitmentSubModels($year, $make, $model);
//        if (!count($this->_submodels) && $year && $make && $model) {
//            $this->_params['FitmentYear'] = $year;
//            $this->_params['FitmentMakeID'] = $make;
//            $this->_params['FitmentModelID'] = $model;
//
//            $this->_modelID = $model;
//
//            $this->_submodels = $this->_client->FitmentSubModels($this->_params)->FitmentSubModelsResult->FitmentSubModels->FitmentSubModel;
//            $this->cacheFitmentSubModels($year, $make, $model, $this->_submodels);
//        }
//
//        return $this->_submodels;
//    }
//    public function fitmentQualifiers($year, $make, $model, $submodel) {
//        $qualifiers = $this->getCachedFitmentQualifiers($year, $make, $model, $submodel);
//
//        if (!count($qualifiers)) {
//            $this->_params['FitmentYear'] = $year;
//            $this->_params['FitmentMakeID'] = $make;
//            $this->_params['FitmentModelID'] = $model;
//            $this->_params['FitmentSubModelID'] = $submodel;
//
//            $this->_submodelID = $submodel;
//
//            $this->_skus_no_qualifiers = $this->retrieveVehicleParts($year, $make, $model, $submodel, null, null);
//            $qualifiers = $this->_client->FitmentQualifiers($this->_params)->FitmentQualifiersResult->Qualifiers;
//
//            $this->cacheFitmentQualifiers($year, $make, $model, $submodel, $qualifiers);
//        }
//
//        return $qualifiers;
//    }

    public function setFilteredQualifiers($params, $qualifiers) {
        $filtered_qualifiers = $this->recurseQualifiers($params, $qualifiers);
    }

    private function cleanDuplicateSkus($_raw_skus) {
        $_skus = array();

        foreach ($_raw_skus as $part) {
            if (!in_array($part->PartNumber, $_skus)) {
                array_push($_skus, $part->PartNumber);
            }
        }

        return $_skus;
    }

    private function recurseQualifiers($params, $qualifiers) {
        foreach ($qualifiers as $_qualifiers) {
            if (is_array($_qualifiers)) {
                foreach ($_qualifiers as $key => $qualifier) {
                    foreach ($qualifier->QualifierValues->FitmentValue as $_qualifier) {
                        $fitmentValues[$key] = array(
                            'vals' => array(
                                'FitmentValue' => array(
                                    'ID' => $_qualifier->ID,
                                    'Name' => $_qualifier->Name
                                )
                            )
                        );

                        $params['FitmentValues'] = $fitmentValues;
                        $skus = $this->_retrieveVehicleQParts($params);
                        $this->_compareFitmentQualifierProducts($skus, $key, $qualifier->QualifierDescription);
                    }
                }
            } else {
                foreach ($_qualifiers->QualifierValues->FitmentValue as $key => $_qualifier) {
                    $fitmentValues[] = array(
                        'vals' => array(
                            'FitmentValue' => array(
                                'ID' => $_qualifier->ID,
                                'Name' => $_qualifier->Name
                            )
                        )
                    );

                    $params['FitmentValues'] = $fitmentValues;

                    $skus = $this->_retrieveVehicleQParts($params);
                    $this->_compareFitmentQualifierProducts($skus, 0, $_qualifiers->QualifierDescription);
                }
            }
        }
    }

    private function _recursiveQualifierMatch($qualifiers) {
        foreach ($qualifiers as $qualifier) {
            $fitmentValues[$key] = array(
                'vals' => array(
                    'FitmentValue' => array(
                        'ID' => $_qualifier->ID,
                        'Name' => $_qualifier->Name
                    )
                )
            );
        }
    }

    private function _compareFitmentQualifierProducts($skus_with_qualifiers,
            $key, $qualifier) {
        if (count($this->_skus_no_qualifiers) > count($skus_with_qualifiers)) {
            array_push($this->_qualifiers, $qualifier);
        }
    }

    public function fitmentBaseVehicleId($year, $make, $model, $submodel) {
        $baseVehicleId = array();

        foreach ($this->_fitments as $fitment) {
            if ($fitment->YearID == $year &&
                    strtolower(trim($fitment->MakeName)) == strtolower(trim($make)) &&
                    strtolower(trim($fitment->ModelName)) == strtolower(trim($model)) &&
                    strtolower(trim($fitment->SubModelName)) == strtolower(trim($submodel))
            ) {
                $baseVehicleId[] = $fitment->VehicleID;
            }
        }

        return $baseVehicleId;
    }

//
//    public function getFitmentFilterYear($year) {
//        
//    }

    public function hasFitment() {
        $submodelID = $this->_request->getParam('submodel');

        if (intval($submodelID)) {
            return true;
        }

        return false;
    }

//    public function clearCache() {
//        $resource = $this->_resource; //Mage::getSingleton('core/resource');
//        $connW = $resource->getConnection('core_write');
//
//        $query = "DELETE FROM `api_fitment` WHERE 1";
//        $connW->query($query);
//
//        $query = "DELETE FROM `api_fitment_years` WHERE 1";
//        $connW->query($query);
//        $query = "DELETE FROM `api_fitment_makes` WHERE 1";
//        $connW->query($query);
//        $query = "DELETE FROM `api_fitment_models` WHERE 1";
//        $connW->query($query);
//        $query = "DELETE FROM `api_fitment_submodels` WHERE 1";
//        $connW->query($query);
//        $query = "DELETE FROM `api_fitment_qualifiers` WHERE 1";
//        $connW->query($query);
//
//        return 'cleared!';
//    }
}
