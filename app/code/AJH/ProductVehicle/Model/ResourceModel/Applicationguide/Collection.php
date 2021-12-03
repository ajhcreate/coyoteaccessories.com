<?php

namespace AJH\ProductVehicle\Model\ResourceModel\Applicationguide;

use AJH\ProductVehicle\Model\ResourceModel\Applicationguide as ResourceModel;
use AJH\ProductVehicle\Model\Applicationguide as Model;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {

    public function _construct() {
        $this->_init(Model::class, ResourceModel::class);
    }

    public function addVehicleInfoByPartClassId($parClassId) {
        $vwvehiclestpmsTable = 'vwvehiclestpms3';
        // join applicationguide table to vwvehiclestpms
        $this->getSelect()->join(
                array(
            'vwvehiclestpms3' => $vwvehiclestpmsTable
                ), 'vwvehiclestpms3.PartMasterID=`main_table`.PartMasterID', array(
            'years' => new Zend_Db_Expr('group_concat(DISTINCT vwvehiclestpms3.YearID SEPARATOR ", ")'),
            'makes' => new Zend_Db_Expr('group_concat(DISTINCT vwvehiclestpms3.MakeName SEPARATOR ", ")'),
            'models' => new Zend_Db_Expr('group_concat(DISTINCT vwvehiclestpms3.ModelName SEPARATOR ", ")'),
            'submodels' => new Zend_Db_Expr('group_concat(DISTINCT vwvehiclestpms3.SubModelName SEPARATOR ", ")')
                )
        );

        $this->getSelect()
                ->group(array('vwvehiclestpms3.MakeName', 'vwvehiclestpms3.ModelName', 'vwvehiclestpms3.SubModelName'));
        $this->addFieldToSelect('PartClassID');
        $this->getSelect()->order('makes');
        $this->addFieldToFilter('PartClassID', array('eq' => $parClassId));
        return $this;
    }

}
