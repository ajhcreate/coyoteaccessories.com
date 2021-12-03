<?php

namespace AJH\Fitment\Model\Catalog;

class Banner extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {

    public function _construct() {}

    public function fitmentMakeName() {
        $_makeID = Mage::app()->getRequest()->getParam('make');
        $_year = Mage::app()->getRequest()->getParam('year');

        $makes = Mage::getModel('fitment/fitment_api')->getCachedFitmentMakes($_year);

        foreach ($makes as $make) {
            if ($make->MakeID === $_makeID) {
                return $make->MakeName;
            }
        }

        return null;
    }
    
    public function fitmentMake() {
        $_makeID = Mage::app()->getRequest()->getParam('make');
        $_year = Mage::app()->getRequest()->getParam('year');

        $makes = Mage::getModel('fitment/fitment_api')->getCachedFitmentMakes($_year);

        foreach ($makes as $make) {
            if ($make->MakeID === $_makeID) {
                return $make;
            }
        }

        return null;
    }

    public function fitmentModel() {
        $_makeID = Mage::app()->getRequest()->getParam('make');
        $_year = Mage::app()->getRequest()->getParam('year');

        $_modelID = Mage::app()->getRequest()->getParam('model');

        $models = Mage::getModel('fitment/fitment_api')->getCachedFitmentModels($_year, $_makeID);

        foreach ($models as $model) {
            if ($model->ModelID === $_modelID) {
                return $model;
            }
        }

        return null;
    }
    
    public function fitmentSubModel() {
        $_makeID = Mage::app()->getRequest()->getParam('make');
        $_year = Mage::app()->getRequest()->getParam('year');
        $_modelID = Mage::app()->getRequest()->getParam('model');
        $_submodelID = Mage::app()->getRequest()->getParam('submodel');

        $submodels = Mage::getModel('fitment/fitment_api')->getCachedFitmentSubModels($_year, $_makeID, $_modelID);

        foreach ($submodels as $submodel) {
            if ($submodel->SubModelID === $_submodelID) {
                return $submodel;
            }
        }

        return null;
    }
    
    public function fitmentCategory() {
        $category = Mage::app()->getRequest()->getParam('cat');        

        return $category;
    }
    
    public function fitmentQualifiers() {
        $qualifiers = Mage::app()->getRequest()->getParam('qualifiers[]');
        $_qualifiers = Mage::app()->getRequest()->getParam('_qualifiers[]');

        return 'qualifiers[]='.$qualifiers.'_qualifiers[]='.$_qualifiers;
    }

    public function fitmentMakeID() {
        $_makeID = Mage::app()->getRequest()->getParam('make');

        return $_makeID;
    }

    public function fitmentYear() {
        $_year = Mage::app()->getRequest()->getParam('year');

        return $_year;
    }

}
