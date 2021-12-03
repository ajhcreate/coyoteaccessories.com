<?php

namespace AJH\PageWarmer\Model;

use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use AJH\Fitment\Model\YearsFactory as FitmentYears;
use AJH\Fitment\Model\MakesFactory as FitmentMakes;
use AJH\Fitment\Model\ModelsFactory as FitmentModels;
use AJH\Fitment\Model\SubModelsFactory as FitmentSubModels;
use AJH\Fitment\Model\Fitment\Api as FitmentApi;

class Fitment extends \Magento\Framework\Model\AbstractModel {

    protected $_years, $_makes, $_models, $_submodels;
    private $fitmentApi;
    protected $_years_arr = [];
    protected $_makes_arr = [];
    protected $_models_arr = [];
    protected $_urls_arr = [];

    public function __construct(
    Context $context, Registry $registry, FitmentYears $fitmentYears,
            FitmentMakes $fitmentMakes, FitmentModels $fitmentModels,
            FitmentSubModels $fitmentSubModels, FitmentApi $fitmentApi
    ) {
        parent::__construct($context, $registry);

        $this->_years = $fitmentYears;
        $this->_makes = $fitmentMakes;
        $this->_models = $fitmentModels;
        $this->_submodels = $fitmentSubModels;

        $this->fitmentApi = $fitmentApi;
    }

    public function getYears() {
        //load updated years from the API
        $years = $this->fitmentApi->getYears();

        //loop and add missing years to the year database table
        foreach ($years as $year) {
            $this->addYear($year);
        }

        $model = $this->_years->create();
        $collection = $model->getCollection();
        $collection->setPageSize(5);
//        $collection->addFieldToFilter('year', 2019);       

        return $collection;
    }

    public function addYear($year) {
        $data_added = 0;

        $model = $this->_years->create();
        $collection = $model->getCollection();
        $collection->addFieldToFilter('year', $year);

        if (!$collection->getSize()) {
            //insert year to api_fitment_years table
            $model->addData(["year" => $year]);

            $saveData = $model->save();

            if ($saveData) {
                $data_added++;
            }
        }

        return $data_added;
    }

    public function getMakes($year) {      
        

        //load updated makes from the API
        $makes = $this->fitmentApi->getMakesByYear($year);                

        foreach ($makes as $make) {            
            $this->addMake($year, $make);
        }

        $model = $this->_makes->create();
        $collection = $model->getCollection();
        $collection->addFieldToFilter('year', $year);

        return $collection;
    }

    public function addMake($year, $make) {
        $data_added = 0;    

        $model = $this->_makes->create();
        $collection = $model->getCollection();
        $collection->addFieldToFilter('year', $year);
        $collection->addFieldToFilter('makeID', $make->MakeID);          

        if (!$collection->getSize()) {
            //insert year to api_fitment_years table
            $model->addData(["year" => $year]);
            $model->addData(["makeID" => $make->MakeID]);
            $model->addData(["makeName" => $make->MakeName]);
            $model->addData(["makeLogo" => $make->LOGO_URL]);
            $model->addData(["make" => json_encode($make)]);

            $saveData = $model->save();

            if ($saveData) {                
                $data_added++;
            }
        }

        return $data_added;
    }

    public function _getMakes($year) {

        $collection = $this->getMakes($year);

        foreach ($collection as $make) {
            
        }

        return $collection;
    }

    public function getModels($year, $makeID) {
        $models = $this->_models->create();
        $collection = $models->getCollection();
        $collection->addFieldToFilter('year', $year);
        $collection->addFieldToFilter('makeID', $makeID);

        return $collection;
    }
    
    public function addModel($year, $make, $model) {
        $data_added = 0;        

        $model = $this->_makes->create();
        $collection = $model->getCollection();
        $collection->addFieldToFilter('year', $year);
        $collection->addFieldToFilter('makeID', $make->MakeID);          

        if (!$collection->getSize()) {
            //insert year to api_fitment_years table
            $model->addData(["year" => $year]);
            $model->addData(["makeID" => $make->MakeID]);
            $model->addData(["makeName" => $make->MakeName]);
            $model->addData(["makeLogo" => $make->LOGO_URL]);
            $model->addData(["make" => json_encode($make)]);

            $saveData = $model->save();

            if ($saveData) {                
                $data_added++;
            }
        }

        return $data_added;
    }

    public function getSubModels($year, $makeID, $modelID) {
        $years = $this->_submodels->create();
        $collection = $years->getCollection();
        $collection->addFieldToFilter('year', $year);
        $collection->addFieldToFilter('makeID', $makeID);

        return $collection;
    }

    public function getFitmentUrls($fitment = []) {
        $url = "fitment/index/categories/?year={$fitment['year']}&make={$fitment['make']}&model={$fitment['model']}&submodel={$fitment['model']}&qualifiers[]=1&_qualifiers[]=";

        return $url;
    }

    public function generateFitmentUrls() {
        $makes = [];
        $years = $this->getYears();

        foreach ($years as $year) {
            $_year = $year->getData();

            $this->_makes_arr[$_year['year']] = $this->getMakes($_year['year']);
        }

        return $makes;
    }

    public function generateFitmentModels() {
        $makes = [];
        $years = $this->getYears();

        foreach ($years as $year) {
            $_year = $year->getData();

            $makes = $this->getMakes($_year['year']);
        }

        return $makes;
    }

}
