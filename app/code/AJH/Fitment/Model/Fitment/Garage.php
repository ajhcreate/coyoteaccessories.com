<?php

namespace AJH\Fitment\Model\Fitment;

use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\App\RequestInterface;
use AJH\Fitment\Model\Fitment\Categories as FitmentCategories;
use AJH\Fitment\Model\Fitment;

class Garage extends \Magento\Framework\Model\AbstractModel {

    protected $_year, $_make, $_model, $_submodel, $_qualifiers, $_qualifiers2;
    protected $_fitmentApi;
    protected $_garageVehicles;
    protected $_coreSession;
    protected $_request;
    protected $_requestHttp;
    protected $_filters;
    protected $_fitmentCategories;
    protected $_fitmentHelper;
    private $fitment;

    public function __construct(Fitment $fitment,
            SessionManagerInterface $coreSession, RequestInterface $request,
            FitmentCategories $fitmentCategories
    ) {

        $this->_request = $request;

        $this->_year = $this->_request->getParam('year');
        $this->_make = $this->_request->getParam('make');
        $this->_model = $this->_request->getParam('model');
        $this->_submodel = $this->_request->getParam('submodel');

        $this->_qualifiers = $this->_request->getParam('qualifiers');
        $this->_qualifiers2 = $this->_request->getParam('_qualifiers');

        $this->fitment = $fitment;

        $this->_coreSession = $coreSession;
        $this->_fitmentCategories = $fitmentCategories;

        $this->_coreSession->start();

        $this->_garageVehicles = $this->_coreSession->getFitmentGarage();
    }

    public function getMake() {
        return $this->fitment->getMake();
    }

    public function getModel() {
        return $this->fitment->getModel();
    }

    public function getSubModel() {
        return $this->fitment->getSubModel();
    }

    public function loadFitmentFilters() {
        $qualifiers = is_array($this->_qualifiers) ? implode(",", $this->_qualifiers) : "";
        $_qualifiers = is_array($this->_qualifiers2) ? implode(",", $this->_qualifiers2) : "";

        $this->_filters = array(
            'year' => $this->_year,
            'make' => $this->getMake(),
            'model' => $this->getModel(),
            'submodel' => $this->getSubModel(),
            'qualifiers' => $this->_qualifiers,
            '_qualifiers' => $this->_qualifiers2,
            'params' => "year={$this->_year}&make={$this->_make}&model={$this->_model}&submodel={$this->_submodel}&qualifiers=" . $qualifiers . "&_qualifiers=" . $_qualifiers
        );

        return $this->_filters;
    }

    public function reindexGarage() {
        $this->_coreSession->start();
        $vehicles = array();

        if ($this->_year && $this->_make && $this->_model && $this->_submodel) {
            $this->setNewVehicleToGarage();
        }

        $garage_vehicles = $this->_coreSession->getFitmentGarage();
        $vehicle_id = $this->_year . '-' . $this->_make . '-' . $this->_model . '-' . $this->_submodel;

        $count = 0;
        if (isset($garage_vehicles) && count($garage_vehicles)) {
            foreach ($garage_vehicles as $vehicle) {

                $qualifiers = ''; //is_array($vehicle['qualifiers']) && isset($vehicle['qualifiers'][0])?strtolower($vehicle['qualifiers'][0]):'';
                $_qualifiers = '';

                if (isset($vehicle['year'])) {
                    $index = $vehicle['year'] . $vehicle['make']['ID'] . $vehicle['model']['ID'] . $vehicle['submodel']['ID'] . str_replace(' ', '', $qualifiers);
                    $vehicles[$index] = $vehicle;
                    $vehicles[$index]['id'] = $vehicle['year'] . '-' . $vehicle['make']['ID'] . '-' . $vehicle['model']['ID'] . '-' . $vehicle['submodel']['ID'];
                    $vehicles[$index]['name'] = $vehicle['year'] . ' ' . $vehicle['make']['Name'] . ' ' . $vehicle['model']['Name'] . ' ' . $vehicle['submodel']['Name'];
                    $vehicles[$index]['params'] = array(
                        'year' => $vehicle['year'],
                        'make' => $vehicle['make']['ID'],
                        'model' => $vehicle['model']['ID'],
                        'submodel' => $vehicle['submodel']['ID'],
                        'qualifiers[]' => str_replace(' ', '', $qualifiers),
                        '_qualifiers[]' => str_replace(' ', '', $_qualifiers)
                    );
                }
                $count++;
            }

            $vehicles['count'] = $count;
            $vehicles['current'] = $vehicle_id;

            $this->_garageVehicles = $vehicles;
        }
    }

    public function setNewVehicleToGarage() {
        $this->_coreSession->start();

        $filters = $this->loadFitmentFilters();

        $qualifiers = is_array($filters['qualifiers']) && isset($filters['qualifiers'][0]) ? strtolower($filters['qualifiers'][0]) : '';

        $index = $filters['year'] . $filters['make']['ID'] . $filters['model']['ID'] . $filters['submodel']['ID'] . str_replace(' ', '', $qualifiers);

        $this->_garageVehicles[$index] = $filters;

        $this->_coreSession->setFitmentGarage($this->_garageVehicles);
    }

    public function getGarageVehicles() {
        $this->reindexGarage();

        return $this->_garageVehicles;
    }

    public function removeVehicleFromGarage() {        
        $vehicle_id = $this->_request->getParam('vehicle_id');
        $vehicles = $this->_garageVehicles;
        
        $response = ['status' => 0, 'vehicles' => json_encode($vehicles)];

        if (isset($vehicles[$vehicle_id]) ) {
            unset($vehicles[$vehicle_id]);

            $response = [
                'status' => 1,
                'vehicles' => json_encode($vehicles)
                ];
        }

        $this->_coreSession->setFitmentGarage($vehicles);

        return $response;
    }

}
