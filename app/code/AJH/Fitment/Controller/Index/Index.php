<?php

namespace AJH\Fitment\Controller\Index;

class Index extends \Magento\Framework\App\Action\Action {

    protected $_pageFactory;

    public function __construct(
    \Magento\Framework\App\Action\Context $context, \Magento\Framework\View\Result\PageFactory $pageFactory) {
        $this->_pageFactory = $pageFactory;
        return parent::__construct($context);
    }

    public function execute() {
        return $this->_pageFactory->create();
    }

    public function indexAction() {
        $this->loadLayout()->renderLayout();
    }

    public function categoriesAction() {
        $this->loadLayout();
        if ((bool) $this->getRequest()->getParam('isajax')) { // ?ajax=true
            $this->getLayout()->getBlock('root')->setTemplate('page/content.phtml');  //changes the root template
        }
        $this->renderLayout();
    }

    public function allAction() {
        $apiModel = Mage::getSingleton('AJH_Fitment_Model_Fitment_Api');
        $items = $apiModel->_fitments;

        echo '<table border="1">';
        echo '<tr><td>#</td><td><strong>' . implode('</strong></td><td><strong>', array_keys((array) $items[0])) . '</strong></td></tr>';
        foreach ($items as $key => $item) {
            echo '<tr>';
            echo '<td>' . ($key + 1) . '</td>';
            echo '<td>' . $item->MakeName . '</td>';
            echo '<td>' . $item->YearID . '</td>';
            echo '<td>' . $item->ModelName . '</td>';
            echo '<td>' . $item->SubModelName . '</td>';
            echo '<td>' . $item->BaseVehicleID . '</td>';
            echo '<td><a target="_blank" href="' . Mage::getUrl('coyote_fitment/index/part') . '?vehicleId=' . $item->VehicleID . '">' . $item->VehicleID . '</a></td>';
            echo '<tr/>';
        }
        echo '</table>';
        die;
    }

    public function showallAction() {
        $fitment = Mage::getSingleton('AJH_Fitment_Model_Fitment_Api');
        $fitment->loadAllFitment();
    }

    public function partAction() {

        $baseVehicleIds[] = $this->getRequest()->getParam('vehicleId');

        $apiModel = Mage::getSingleton('AJH_Fitment_Model_Fitment_Api');
        $items = $apiModel->retrievePartByVehicleID($baseVehicleIds);

        echo '<table border="1">';
        echo '<tr><td>#</td><td>' . implode('</td><td>', array_keys((array) $items[0])) . '</td></tr>';
        foreach ($items as $key => $item) {
            echo '<tr><td>' . ($key + 1) . '</td><td>' . implode("</td><td>", (array) $item) . '</td></tr>';
        }
        echo '</table>';
        die;
    }

    public function makesAction() {
        try {
            $yearId = $this->getRequest()->getParam('year');

            if (!$yearId) {
                die('Year is required');
            }

            Mage::getSingleton('core/session')->setCurrentFitmentYear($yearId);
            $make = Mage::getSingleton('core/session')->getCurrentFitmentMake();

            $fitment = Mage::getSingleton('AJH_Fitment_Model_Fitment_Api');
            $makes = $fitment->fitmentMakes($yearId);

            $fitment->_makes = $makes;

            $options = ['' => 'Make'];

            if (is_array($makes)) {
                foreach ($makes as $key => $value) {
                    $options[$value->MakeID] = $value->MakeName;
                }
            } else {
                $options[$makes->MakeID] = $makes->MakeName;
            }

            echo AJH_Fitment_Helper_Fitment::getOptionsHtml($options, '', $make);
            die;
        } catch (\Exception $e) {
            Mage::logException($e);
        }
    }

    public function makeslistAction() {
        try {
            $yearId = $this->getRequest()->getParam('year');

            if (!$yearId) {
                die('Year is required');
            }

            $fitment = Mage::getSingleton('AJH_Fitment_Model_Fitment_Api');
            $makes = $fitment->_fitmentMakes($yearId);

            if (is_array($makes)) {
                foreach ($makes as $key => $value) {
                    $options[$value->MakeID] = $value->MakeName;
                }
            } else {
                $options[$makes->MakeID] = $makes->MakeName;
            }

            echo AJH_Fitment_Helper_Fitment::getOptionsHtml($options);
            die;
        } catch (\Exception $e) {
            Mage::logException($e);
        }
    }

    public function modelsAction() {
        try {
            $yearId = $this->getRequest()->getParam('year');
            $makeId = $this->getRequest()->getParam('make');
            if (!$yearId) {
                die('Year is required');
            }
            if (!$makeId) {
                die('Make is required');
            }

            Mage::getSingleton('core/session')->setCurrentFitmentMake($makeId);
            $model = Mage::getSingleton('core/session')->getCurrentFitmentModel();

            $fitment = Mage::getSingleton('AJH_Fitment_Model_Fitment_Api');
            $models = $fitment->fitmentModels($yearId, $makeId);

            $fitment->_models = $models;

            $options = ['' => 'Model'];

            if (is_array($models)) {
                foreach ($models as $key => $value) {
                    $options[$value->ModelID] = $value->ModelName;
                }
            } else {
                $options[$models->ModelID] = $models->ModelName;
            }

            echo AJH_Fitment_Helper_Fitment::getOptionsHtml($options, '', $model);
            die;
        } catch (\Exception $e) {
            Mage::logException($e);
        }
    }

    public function submodelsAction() {
        try {
            $yearId = $this->getRequest()->getParam('year');
            $makeName = $this->getRequest()->getParam('make');
            $modelName = $this->getRequest()->getParam('model');

            Mage::getSingleton('core/session')->setCurrentFitmentModel($modelName);
            $submodel = Mage::getSingleton('core/session')->getCurrentFitmentSubModel();

            if (!$yearId) {
                die('Year is required');
            }
            if (!$makeName) {
                die('Make is required');
            }
            if (!$modelName) {
                die('Model is required');
            }

            $fitment = Mage::getSingleton('AJH_Fitment_Model_Fitment_Api');
            $submodels = $fitment->fitmentSubModels($yearId, $makeName, $modelName);

            $options = ['' => 'SubModel'];

            if (is_array($submodels)) {
                foreach ($submodels as $key => $value) {
                    $options[$value->SubModelID] = $value->SubModelName;
                }
            } else {
                $options[$submodels->SubModelID] = $submodels->SubModelName;
            }
            echo AJH_Fitment_Helper_Fitment::getOptionsHtml($options, '', $submodel);
            die;
        } catch (\Exception $e) {
            Mage::logException($e);
        }
    }

    public function qualifiersAction() {

        try {
            $yearId = $this->getRequest()->getParam('year');
            $makeName = $this->getRequest()->getParam('make');
            $modelName = $this->getRequest()->getParam('model');
            $subModelName = $this->getRequest()->getParam('submodel');


            if (!$yearId) {
                die('Year is required');
            }
            if (!$makeName) {
                die('Make is required');
            }
            if (!$modelName) {
                die('Model is required');
            }
            if (!$subModelName) {
                die('SubModel is required');
            }

            Mage::getSingleton('core/session')->setCurrentFitmentSubModel($subModelName);

            $fitment = Mage::getSingleton('AJH_Fitment_Model_Fitment_Api');
            $qualifiers = $fitment->fitmentQualifiers($yearId, $makeName, $modelName, $subModelName);

            $_filtered_qualifiers = $fitment->_qualifiers;

            $html = '';
            $name = '';

            if (is_array($qualifiers->FitmentQualifier)) {
                foreach ($qualifiers->FitmentQualifier as $key => $value) {
                    $qualifiers_count = count($value->QualifierValues->FitmentValue);
                    if ($qualifiers_count) {
//                        if (in_array($_filtered_qualifiers, $value->QualifierDescription)) {
                        $name = str_replace(' ', '_', strtolower($value->QualifierDescription));
                        $onchange = $qualifiers_count > 1 ? "onchange=\"checkQualifiers(this)\"" : "";
                        $html .= '<select name="qualifiers[]" id="' . $name . ' ' . $onchange . '>';
                        $options = ['' => $value->QualifierDescription];

                        foreach ($value->QualifierValues->FitmentValue as $fitmentValue) {
                            $options[$fitmentValue->ID] = $fitmentValue->Name;
                        }
                        $html .= AJH_Fitment_Helper_Fitment::getOptionsHtml($options, $value->QualifierDescription);
                        $html .= '</select>';
//                        }
                    }

                    if ($html === '') {
                        $html .= '<select name="qualifiers[]" id="no-qualifiers" style="display: none;" disabled="disabled"><option>No Qualifiers</option></select>';
                    }
                }
            } else {


//                if (count($qualifiers->FitmentQualifier->QualifierValues->FitmentValue) && in_array($_filtered_qualifiers, $qualifiers->FitmentQualifier->QualifierDescription)) {
                if (count($qualifiers->FitmentQualifier->QualifierValues->FitmentValue)) {
                    $name = str_replace(' ', '_', strtolower($qualifiers->FitmentQualifier->QualifierDescription));
                    $html .= '<select name="qualifiers[]" id="' . $name . '">';
                    $options = ['' => $qualifiers->FitmentQualifier->QualifierDescription];

                    foreach ($qualifiers->FitmentQualifier->QualifierValues->FitmentValue as $fitmentValue) {
                        $options[$fitmentValue->ID] = $fitmentValue->Name;
                    }

                    $html .= AJH_Fitment_Helper_Fitment::getOptionsHtml($options, $qualifiers->FitmentQualifier->QualifierDescription);
                    $html .= '</select>';
                } else {
                    $html .= '<select name="qualifiers[]" id="no-qualifiers" style="display: none;" disabled="disabled"><option>No Qualifiers</option></select>';
                }
            }

            echo $html;
            die;
        } catch (\Exception $e) {
            Mage::logException($e);
        }
    }

    public function _qualifiersAction() {

        try {
            $yearId = $this->getRequest()->getParam('year');
            $makeName = $this->getRequest()->getParam('make');
            $modelName = $this->getRequest()->getParam('model');
            $subModelName = $this->getRequest()->getParam('submodel');

            if (!$yearId) {
                die('Year is required');
            }
            if (!$makeName) {
                die('Make is required');
            }
            if (!$modelName) {
                die('Model is required');
            }
            if (!$subModelName) {
                die('SubModel is required');
            }

            $fitment = Mage::getSingleton('AJH_Fitment_Model_Fitment_Api');
            $qualifiers = $fitment->fitmentQualifiers($yearId, $makeName, $modelName, $subModelName);

            $_filtered_qualifiers = $fitment->_qualifiers;

            $html = '';
            $name = '';

            if (is_array($qualifiers->FitmentQualifier)) {
                foreach ($qualifiers->FitmentQualifier as $key => $value) {
                    $qualifiers_count = count($value->QualifierValues->FitmentValue);
                    if ($qualifiers_count) {
//                        if (in_array($_filtered_qualifiers, $value->QualifierDescription)) {
                        $name = str_replace(' ', '_', strtolower($value->QualifierDescription));
                        $onchange = $qualifiers_count > 1 ? "onchange=\"checkQualifiers(this)\"" : "";
                        $html .= '<select name="qualifiers[]" id="' . $name . ' ' . $onchange . '>';
                        $options = ['' => $value->QualifierDescription];

                        foreach ($value->QualifierValues->FitmentValue as $fitmentValue) {
                            $options[$fitmentValue->ID] = $fitmentValue->Name;
                        }
                        $html .= AJH_Fitment_Helper_Fitment::getOptionsHtml($options);
                        $html .= '</select>';
//                        }
                    }

                    if ($html === '') {
                        $html .= '<select name="qualifiers[]" id="no-qualifiers" style="display: none;" disabled="disabled"><option>No Qualifiers</option></select>';
                    }
                }
            } else {


//                if (count($qualifiers->FitmentQualifier->QualifierValues->FitmentValue) && in_array($_filtered_qualifiers, $qualifiers->FitmentQualifier->QualifierDescription)) {
                if (count($qualifiers->FitmentQualifier->QualifierValues->FitmentValue)) {
                    $name = str_replace(' ', '_', strtolower($qualifiers->FitmentQualifier->QualifierDescription));
                    $html .= '<select name="qualifiers[]" id="' . $name . '">';
                    $options = ['' => $qualifiers->FitmentQualifier->QualifierDescription];

                    foreach ($qualifiers->FitmentQualifier->QualifierValues->FitmentValue as $fitmentValue) {
                        $options[$fitmentValue->ID] = $fitmentValue->Name;
                    }

                    $html .= AJH_Fitment_Helper_Fitment::getOptionsHtml($options);
                    $html .= '</select>';
                } else {
                    $html .= '<select name="qualifiers[]" id="no-qualifiers" style="display: none;" disabled="disabled"><option>No Qualifiers</option></select>';
                }
            }

            echo $html;
            die;
        } catch (\Exception $e) {
            Mage::logException($e);
        }
    }

    public function partByVehicleIdAction() {
        $isAjax = $this->getRequest()->isXmlHttpRequest();

        try {
            $yearId = $this->getRequest()->getParam('year');
            $makeName = $this->getRequest()->getParam('make');
            $modelName = $this->getRequest()->getParam('model');
            $submodelName = $this->getRequest()->getParam('submodel');

            if (!$yearId) {
                die('Year is required');
            }
            if (!$makeName) {
                die('Make is required');
            }
            if (!$modelName) {
                die('Model is required');
            }
            if (!$submodelName) {
                die('SubModel is required');
            }
//
            $fitment = Mage::getSingleton('AJH_Fitment_Model_Fitment_Api');
            $baseVehicleIds = $fitment->fitmentBaseVehicleId($yearId, $makeName, $modelName, $submodelName);

            //note: find way to assign _partByVehicleIDs
            $parts = $fitment->retrievePartByVehicleID($baseVehicleIds);
//            var_dump($baseVehicleIds);
//            $fitment_categories = Mage::getModel('AJH_Fitment_Model_Fitment_Categories')->getFitmentCategoryProducts();

            $output = $this->getLayout()->createBlock('fitment/fitment_results')->setTemplate('ajh/fitment/categories.phtml')->toHtml();

            echo $output;

//            echo AJH_Fitment_Helper_Fitment::getTableHtml($parts);
//            $this->loadLayout()->renderLayout();

            die;
        } catch (\Exception $e) {
            Mage::logException($e);
        }
    }

    public function retrieveVehiclePartsAction() {
        $this->loadLayout()->renderLayout();
    }

    public function partsAction() {
        $this->loadLayout()->renderLayout();
    }

}
