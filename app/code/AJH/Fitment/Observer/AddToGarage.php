<?php

namespace AJH\Fitment\Observer;

use AJH\Fitment\Model\Fitment;
use Magento\Framework\Session\SessionManagerInterface as CoreSession;

class AddToGarage implements \Magento\Framework\Event\ObserverInterface {

    private $logger;
    private $_fitment;
    private $_coreSession;

    public function __construct(\Psr\Log\LoggerInterface $logger,
            Fitment $fitment, CoreSession $coreSession) {
        $this->logger = $logger;
        $this->_fitment = $fitment;
        $this->_coreSession = $coreSession;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
        $garage_vehicles = [];
        $garage = $this->_coreSession->getFitmentGarage();

        $_fitment = $observer->getData('fitment');
        $params = ['YearID' => $_fitment['YearID'], 'MakeID' => $_fitment['MakeID'], 'ModelID' => $_fitment['ModelID'], 'SubModelID' => $_fitment['SubModelID']];

        $year = $_fitment['YearID'];
        $make = $this->_fitment->getMake($params);
        $model = $this->_fitment->getModel($params);        
        $submodel = $this->_fitment->getSubModel($params);
        $qualifier = $this->_fitment->getQualifier($params);

        $path = ''; //$url->getPath();        

        $vid = $year . $make['ID'] . $model['ID'] . $submodel['ID'];

        $_fitment['id'] = $vid;
        $_fitment['current'] = $vid;
        $_fitment['name'] = $year . ' ' . $make['Name'] . ' ' . $model['Name'] . ' ' . $submodel['Name'];

        $_fitment['url'] = "{$path}?year={$year}&make={$make['ID']}&model={$model['ID']}&submodel={$submodel['ID']}";
        if (is_array($qualifier[0]) && is_array($qualifier[1])) {
            $_fitment['url'] .= "&qualifiers[]=" . implode(",", $qualifier[0]) . "&_qualifiers[]=" . implode(",", $qualifier[1]);
        }

        if (!is_null($garage) && count($garage)) {
            foreach ($garage as $key => $vehicle) {
                $garage_vehicles[$key] = $vehicle;
            }
        }

        $garage_vehicles[$vid] = $_fitment;
        $this->_coreSession->setFitmentGarage($garage_vehicles);
        $this->_coreSession->setSelectedVehicle($params);

        $this->logger->info('Fitment Garage', ['data' => $garage_vehicles]);

        return $this;
    }

}
