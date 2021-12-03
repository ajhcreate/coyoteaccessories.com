<?php

namespace AJH\Fitment\Block;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;
use Magento\Framework\View\Element\Template\Context;
use AJH\Fitment\Model\Fitment\Garage as GarageModel;
use AJH\Fitment\Model\Fitment;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Garage extends Template implements BlockInterface {

    protected $_template = "garage.phtml";
    protected $_garage, $_request, $_storeManager, $_scopeConfig;
    private $fitment;

    public function __construct(Context $context, GarageModel $garage,
            RequestInterface $request, Fitment $fitment,
            StoreManagerInterface $storeManager,
            ScopeConfigInterface $scopeConfig) {
        parent::__construct($context);

        $this->_garage = $garage;
        $this->_request = $request;
        $this->fitment = $fitment;
        $this->_storeManager = $storeManager;
        $this->_storeManager = $storeManager;
    }

    public function _prepareLayout() {
        
    }

    public function getGarageVehicle() {
        return $this->_garage->getGarageVehicles();
    }

    public function getVehicles() {
        $garage = $this->_garage->getGarageVehicles();

        return $garage;
    }

    public function getVehiclesByMake() {
        $vehicles = $this->getVehicles();

        $_vehicles = [];

        if (isset($vehicles['count']) && (int) $vehicles['count']) {
            foreach ($vehicles as $key => $vehicle) {
                if (isset($vehicle['make'])) {
                    $makeID = $vehicle['make']['ID'];
                    $_vehicles[$makeID][] = array_merge($vehicle, array('key' => $key));
                }
            }
        }

        return $_vehicles;
    }

    public function getVehicleMake($year, $makeId) {
        $this->_request->setPostValue('year', $year);
        $_makes = $this->fitment->getMakes();

        $_make = null;

        foreach ($_makes as $make) {
            if ((int) $make['ID'] === (int) $makeId) {
                $_make = $make;
            }
        }

        return $_make;
    }

    private function getVehicleByID($vehicleID) {
        $vehicles = $this->getVehicles();

        foreach ($vehicles['garage'] as $vehicle_id => $vehicle) {
            if ($vehicle_id == $vehicleID) {
                return $vehicle;
            }
        }

        return;
    }

    public function isCurrentlySelected($year, $make, $model, $submodel) {
        $_year = $this->_request->getParam('year');
        $_make = $this->_request->getParam('make');
        $_model = $this->_request->getParam('model');
        $_submodel = $this->_request->getParam('submodel');

        if (intval($year) === intval($_year) && intval($make) === intval($_make) && intval($model) === intval($_model) && intval($submodel) === intval($_submodel)) {
            return TRUE;
        }

        return FALSE;
    }

    public function getVehicleImage($year, $make, $model) {

        $api_key = $this->getAPIKey();

//        if (strtolower(trim($submodel)) === 'base') {
        $url = "http://iconfigurators.app/api/?function=getVehiclePreview&year=$year&make={$make}&model={$model}&key=$api_key";
//        } else {
//            $url = "http://iconfigurators.app/api/?function=getVehicleImages&year=$year&make={$make}&model={$model}&submodel={$submodel}&key=$api_key";
//        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_ENCODING, '');
        $result = curl_exec($ch);
        curl_close($ch);

        $obj = json_decode($result);

        if (!is_null($obj) && isset($obj->vehicles[0])) {
            return $this->checkIfImageExists($obj->vehicles[0]->previewImage);
        }

        return null;
    }

    private function getAPIKey() {
        return 'GSEPXELYCHZLIEIZPSTXSSGWTDCLYZNRYTYSIOIBTLMDGFMXUX';
    }

    public function checkIfImageExists($url) {

        // Creating a variable with an URL 
        // to be checked 
        // Initializing new session 
        $ch = curl_init($url);
        // Request method is set 
        curl_setopt($ch, CURLOPT_NOBODY, true);
        // Executing cURL session 
        curl_exec($ch);
        // Getting information about HTTP Code 
        $retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        // Testing for 404 Error 
        if ($retcode != 200) {
            $url = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product/placeholder/';
        }

        curl_close($ch);

        return $url;
    }

    public function getPlaceholderImage() {
        $image = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product/placeholder/';
        $image .= $this->_scopeConfig->getValue('catalog/placeholder/alt_image_placeholder');
        return $image;
    }

}
