<?php

namespace AJH\D2R\Helper;

use AJH\D2R\Helper\Data as D2RHelperData;

class TPMS extends D2RHelperData {

    protected static $_read = null;

    public function saveTPMSWorksheet($data) {
        $fields = [
            'CustomerName' => 'customerName',
            'CustomerPhone' => 'customerPhone',
            '' => 'customerEmail',
            'DistributorID' => 'distributorName', // ???
            'InstallerShop' => 'installerShop',
            'InstallerName' => 'installerName',
            'InstallerPhone' => 'installerPhone',
            'InstallerFax' => 'installerFax',
            'InstallerEmail' => 'installerEmail',
            'TireSystem' => 'tireSystem',
            'PlacardPressure_Front' => 'placardFront',
            'PlacardPressure_Rear' => 'placardRear',
            'PlacardPressure_Spare' => 'placardSpare',
            'TirePressure_LF' => 'tireLF',
            'TirePressure_RF' => 'tireRF',
            'TirePressure_LR' => 'tireLR',
            'TirePressure_RR' => 'tireRR',
            'TirePressure_Spare' => 'tireSpare',
            'VIN' => 'vin',
            'YearID' => 'vehicleYear',
            'MakeID' => 'vehicleMake',
            'ModelID' => 'vehicleModel',
            'SubModelID' => 'vehicleSubmodel',
            'RelearnProcedureUsed' => 'relearnProc',
            'OtherRelearnDesc' => 'relearnProcOther',
            'SensorPurchased' => 'sensorPartNo',
            'ResetToolUsed' => 'resetToolUsed',
            '' => 'relearnToolUsed',
            '' => 'softwareVersion',
            'WheelType' => 'wheelType',
            'ProximityOfSensors' => 'sensorProximity',
            // extra
            'DateSubmitted' => '', // should be this populated automatically?
            'RecommendedSensors' => '' // do we really need this to be saved into the table? That's not from a user input field
        ];
    }

    /*
      `AddlCriteriaResponse` tinyint(1) NOT NULL DEFAULT '0',
      `MILFlashing` tinyint(1) NOT NULL DEFAULT '0',
      `SensorsInvolved` int(11) NOT NULL DEFAULT '0',
      `WhereIsVehicle` varchar(400) NOT NULL DEFAULT '',
      `StemLocation` varchar(400) NOT NULL DEFAULT '',
      `ChallengeAccurate` varchar(400) NOT NULL DEFAULT '',
      `InstallerStepsTaken` varchar(400) NOT NULL DEFAULT '',
      `OrotekProcedureFollowed` varchar(400) NOT NULL DEFAULT '',
      `TimesRelearnPerformed` varchar(400) NOT NULL DEFAULT '',
      `MakeCovered` varchar(400) NOT NULL DEFAULT '',
      `ToolUpdated` varchar(400) NOT NULL DEFAULT '',
      `DisplaySensorData` varchar(400) NOT NULL DEFAULT '',
      `OESensorForVIN` varchar(400) NOT NULL DEFAULT '',
      `DillOESensor` varchar(400) NOT NULL DEFAULT '',
      `MatchOwnersManual` varchar(400) NOT NULL DEFAULT '',
      `MatchTIA` varchar(400) NOT NULL DEFAULT '',
      `MatchMitchell` varchar(400) NOT NULL DEFAULT '',
      `TPMSChallengeStatusID` int(11) NOT NULL DEFAULT '0',
      `ResolutionID` int(11) NOT NULL DEFAULT '0',
      `Level1_SalesRepID` int(11) NOT NULL DEFAULT '0',
      `Level2_SalesRepID` int(11) NOT NULL DEFAULT '0',
      `RemovedPartNo` varchar(100) NOT NULL DEFAULT '',
      `Notes` varchar(800) NOT NULL DEFAULT '',
      `WebsiteUsersID` bigint(20) NOT NULL DEFAULT '0',
      `UsersID` bigint(20) NOT NULL DEFAULT '0',
      `Level1Timestamp` datetime DEFAULT NULL,
      /* */
}
