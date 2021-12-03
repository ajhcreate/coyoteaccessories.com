<?php

namespace AJH\ProductVehicle\Model\ResourceModel\Parts;

use AJH\ProductVehicle\Model\Parts as Model;
use AJH\ProductVehicle\Model\ResourceModel\Parts as ResourceModel;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {

    public function _construct() {
        $this->_init(Model::class, ResourceModel::class);
    }

    public function addVehicleInfo() {
        $applicationGuideTable = $this->getTable('applicationguide');
        // join partmaster table to application guide
        $this->getSelect()->join(
                array(
            'appguide' => $applicationGuideTable
                ), 'appguide.PartClassID=`main_table`.PartClassID', array(
            'PartMasterID'
                )
        );
        $vwvehiclestpmsTable = 'vwvehiclestpms3';
        // join partmaster table to vwvehiclestpms
        $this->getSelect()->join(
                array(
            'vwvehiclestpms3' => $vwvehiclestpmsTable
                ), 'vwvehiclestpms.PartMasterID=`appguide`.PartMasterID', array(
            'year' => new Zend_Db_Expr('group_concat(vwvehiclestpms3.YearID SEPARATOR ", ")'),
            'MakeName',
            'ModelName',
            'SubModelName'
                )
        );

        $this->getSelect()->group('vwvehiclestpms3.MakeName', 'vwvehiclestpms3.ModelName', 'vwvehiclestpms3.SubModelName');
        $this->getSelect()->limit(20);
        print_r($this->getSelect()->__toString());
    }

    public function addPartNumberFilter($partNumber) {
        $this->addFieldToFilter('PartNumber', array('eq' => $partNumber));
    }

}
