<?php

namespace AJH\Fitment\Model\Fitment;

use AJH\Fitment\Helper\Fitment as FitmentHelper;

class Vehicles extends \Magento\Framework\Model\AbstractModel {

    protected $_fitmentHelper;

    public function __construct(FitmentHelper $fitmentHelper) {
        $this->_fitmentHelper = $fitmentHelper;
    }

    public function getVehicles() {
//        $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
//        $sql = "SELECT * FROM `api_fitment` WHERE 1 ORDER BY `year` DESC";
//        $vehicles = $connection->fetchAll($sql);
//
//        return $vehicles;
        return null;
    }

    public function getVehicleImage($year, $make, $model) {
        $_model = $this->getVehicleModelID($year, $make, $model);

        $api_key = $this->_fitmentHelper->_ApiKey;

        $url = "http://iconfigurators.app/api/?function=getVehiclePreview&year=$year&make={$make}&model={$_model}&key=$api_key";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_ENCODING, '');
        $result = curl_exec($ch);
        curl_close($ch);

        $obj = json_decode($result);
        $_image_url = null;

        if (isset($obj->vehicles)) {
            foreach ($obj->vehicles as $vehicle) {
                $_image_url = isset($vehicle->baseImage) ? $vehicle->baseImage : $vehicle->previewImage;
            }
        }

        return $_image_url;
    }

//    public function getAPIKey() {
//        return 'GSEPXELYCHZLIEIZPSTXSSGWTDCLYZNRYTYSIOIBTLMDGFMXUX';
//    }

    private function getVehicleModelID($year, $make, $model) {
        $api_key = $this->_fitmentHelper->_ApiKey;

        $url = "http://iconfigurators.app/api/?function=getModels&year={$year}&make={$make}&key=$api_key";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_ENCODING, '');
        $result = curl_exec($ch);
        curl_close($ch);

        $obj = json_decode($result);
        $model_ = null;

        if (isset($obj->models)) {
            foreach ($obj->models as $_model) {
                if (strtolower(trim($_model->model)) == strtolower(trim($model))) {
                    $model_ = $_model;
                }
            }
        }

        if ($model_) {
            return $model_->modelid;
        }

        return null;
    }

}
