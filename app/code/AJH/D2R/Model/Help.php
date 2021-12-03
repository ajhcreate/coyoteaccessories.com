<?php
namespace AJH\D2R\Model;

use Magento\Framework\Model\AbstractModel;
use AJH\D2R\Model\ResourceModel\Help as ResourceModel;

class Help extends AbstractModel
{
    protected $_eventPrefix = 'd2r_help';


    public function _construct()
    {
        parent::_construct();
        $this->_init(ResourceModel::class);
    }



    public function getFields()
    {
        $fields = [
            'ID',
            'TireSystem',
            'PlacardPressure_Front',
            'PlacardPressure_Rear',
            'PlacardPressure_Spare',
            'TirePressure_LF',
            'TirePressure_RF',
            'TirePressure_LR',
            'TirePressure_RR',
            'TirePressure_Spare',
            'VIN',
            'MakeID',
            'YearID',
            'ModelID',
            'SubModelID',
            'AddlCriteriaResponse',
            'RecommendedSensors',
            'RelearnProcedureUsed',
            'OtherRelearnDesc',
            'SensorPurchased',
            'ResetToolUsed',
            'WheelType',
            'ProximityOfSensors',
            'CustomerName',
            'CustomerPhone',
            'CustomerEmail',
            'DistributorID',
            'InstallerShop',
            'InstallerName',
            'InstallerPhone',
            'InstallerFax',
            'InstallerEmail',
            'MILFlashing',
            'SensorsInvolved',
            'WhereIsVehicle',
            'StemLocation',
            'ChallengeAccurate',
            'InstallerStepsTaken',
            'OrotekProcedureFollowed',
            'TimesRelearnPerformed',
            'MakeCovered',
            'ToolUpdated',
            'DisplaySensorData',
            'OESensorForVIN',
            'DillOESensor',
            'MatchOwnersManual',
            'MatchTIA',
            'MatchMitchell',
            'TPMSChallengeStatusID',
            'ResolutionID',
            'Level1_SalesRepID',
            'Level2_SalesRepID',
            'RemovedPartNo',
            'Notes',
            'WebsiteUsersID',
            'UsersID',
            'DateSubmitted',
            'Level1Timestamp',
            'Notes',
            'store_id',
            'store_code'
        ];
        return $fields;
    }



    public function getTranslationTable()
    {
        return [
            'customerName' => 'CustomerName',
            'customerPhone' => 'CustomerPhone',
            'customerEmail' => 'CustomerEmail',
            'distributorId' => 'DistributorID',
            'installerShop' => 'InstallerShop',
            'installerName' => 'InstallerName',
            'installerPhone' => 'InstallerPhone',
            'installerFax' => 'InstallerFax',
            'installerEmail' => 'InstallerEmail',
            'tireSystem' => 'TireSystem',
            'placardFront' => 'PlacardPressure_Front',
            'placardRear' => 'PlacardPressure_Rear',
            'placardSpare' => 'PlacardPressure_Spare',
            'tireLF' => 'TirePressure_LF',
            'tireRF' => 'TirePressure_RF',
            'tireLR' => 'TirePressure_LR',
            'tireRR' => 'TirePressure_RR',
            'tireSpare' => 'TirePressure_Spare',
            'vin' => 'VIN',
            'vehicleYearId' => 'YearID',
            'vehicleMakeId' => 'MakeID',
            'vehicleModelId' => 'ModelID',
            'vehicleSubmodelId' => 'SubModelID',
            'relearnProc' => 'RelearnProcedureUsed',
            'relearnProcOther' => 'OtherRelearnDesc',
            'sensorPartNo' => 'SensorPurchased',
            'resetToolUsed' => 'ResetToolUsed',
            'relearnToolUsed' => 'ResetToolUsed',
            'softwareVersion' => '',
            'wheelType' => 'WheelType',
            'sensorProximity' => 'ProximityOfSensors',
            'tpmsNotes' => 'Notes',
            'storeId' => 'store_id',
            'storeCode' => 'store_code'
//            '' => 'WebsiteUsersID',
//            '' => 'UsersID',
        ];
    }



    public function translate($data)
    {
        $tt = $this->getTranslationTable();
        $res = [];

        foreach($data as $key => $value) {
            if (isset($tt[$key])) {
                $res[$tt[$key]] = $value;
            }
        }

        return $res;
    }



    public function prepareForSave($data)
    {
        $this->addData($this->translate($data));
        //Mage::log(print_r($this->translate($data), true), null, 'tpms.log');
        if (!$this->hasData('DateSubmitted')) {
            $this->setData('DateSubmitted', date("Y-m-d H:i:s"));
        }

        return $this;
    }

}