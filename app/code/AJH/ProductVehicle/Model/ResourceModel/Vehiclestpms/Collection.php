<?php

namespace AJH\ProductVehicle\Model\ResourceModel\Vehiclestpms;

use AJH\ProductVehicle\Model\Vehiclestpms as Model;
use AJH\ProductVehicle\Model\ResourceModel\Vehiclestpms as ResourceModel;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {

    protected function _construct() {
        $this->_init(Model::class, ResourceModel::class);
//        $this->_init('AJH\ProductVehicle\Model\Vehicletpms', 'AJH\ProductVehicle\Model\ResourceModel\Vehicletpms');
    }

//
    public function getVehicles($partnumber) {
        $partmasterTable = $this->getTable('partmaster');
        $partxrefTable = $this->getTable('partxref');

        $this->getSelect()->join(
                array(
            'x' => $partxrefTable
                ), 'x.PartMasterID=`main_table`.PartMasterID', array(
            'XREF_ID'
                )
        );
        $this->getSelect()->join(
                array(
            'p1' => $partmasterTable
                ), 'p1.ID=`main_table`.PartMasterID', array(
            'p1.partnumber',
            'p1.Date_PDQ',
            'p1.Date_ATEQ',
            'p1.Date_Bartec'
                )
        );
        $this->getSelect()->join(
                array(
            'p2' => $partmasterTable
                ), 'p2.ID=`x`.XREF_ID', array(
            'LinkedPart' => 'p2.partnumber',
            'LinkedDate_PDQ' => 'p2.Date_PDQ',
            'LinkedDate_ATEQ' => 'p2.Date_ATEQ',
            'LinkedDate_Bartec' => 'p2.Date_Bartec'
                )
        );

//        $this->getSelect()->where("p2.`Date_PDQ` <> ''");
//        $this->getSelect()->where("p1.`partnumber` = '$partnumber'");

        return $this;
    }

    public function getCAVehicles($partnumber) {
        $partmasterTable = $this->getTable('partmaster');
        $partxrefTable = $this->getTable('partxref');

        $this->getSelect()->join(
                array(
            'x' => $partxrefTable
                ), 'x.PartMasterID=`main_table`.PartMasterID', array(
            'XREF_ID'
                )
        );
        $this->getSelect()->join(
                array(
            'p1' => $partmasterTable
                ), 'p1.ID=`main_table`.PartMasterID', array(
            'p1.partnumber'
                )
        );

        $this->getSelect()->join(
                array(
            'p2' => $partmasterTable
                ), 'p2.ID=`x`.XREF_ID', array(
            'LinkedPart' => 'p2.partnumber'
                )
        );

        return $this;
    }

    public function getCAVehicleInfoByPartNumber($partNumber) {
        $this->getCAVehicles($partNumber);
        $this->addFieldToSelect('MakeName');
        $this->addFieldToSelect('ModelName');
        $this->addFieldToSelect('SubModelName');
        $this->addExpressionFieldToSelect('YearID', new \Zend_Db_Expr('group_concat(DISTINCT YearID ORDER BY YearID ASC SEPARATOR ", ")'), []);
        $this->getSelect()
                ->group(array('MakeName', 'ModelName', 'SubModelName'));
        $this->getSelect()->order('MakeName');
        $this->addFieldToFilter(
                array('p1.partnumber', 'P2.partnumber'), array(
            array('eq' => $partNumber),
            array('eq' => $partNumber)
                )
        );
        return $this;
    }

    /**
     * SELECT v.MakeName, v.ModelName,v.SubModelName, v.YearID ,P1.partnumber, P2.partnumber as LinkedPart FROM vwvehiclestpms v
     * LEFT OUTER JOIN partxref x on x.PartMasterID = v.PartMasterID
     * LEFT OUTER JOIN partmaster p1 on p1.ID = v.PartMasterID
     * LEFT OUTER JOIN partmaster p2 on p2.ID = x.XREF_ID
     * WHERE P1.partnumber IN ('PDQ-001M') OR P2.partnumber IN ('PDQ-001M') ORDER BY v.MakeName
     */
    public function getVehicleInfoByPartNumber($partNumber) {
        $this->getVehicles($partNumber);
        $this->addFieldToSelect('MakeName');
        $this->addFieldToSelect('ModelName');
        $this->addFieldToSelect('SubModelName');
        $this->addExpressionFieldToSelect('YearID', new \Zend_Db_Expr('group_concat(DISTINCT YearID ORDER BY YearID ASC SEPARATOR ", ")'), []);
        $this->getSelect()
                ->group(array('MakeName', 'ModelName', 'SubModelName'));
        $this->getSelect()->order('MakeName');
        $this->addFieldToFilter(
                array('p1.partnumber', 'P2.partnumber'), array(
            array('eq' => $partNumber),
            array('eq' => $partNumber)
                )
        );
        return $this;
    }

    /**
     * SELECT v.MakeName, v.ModelName,v.SubModelName, v.YearID ,P1.partnumber, P2.partnumber as LinkedPart FROM vwvehiclestpms v
     * LEFT OUTER JOIN partxref x on x.PartMasterID = v.PartMasterID
     * LEFT OUTER JOIN partmaster p1 on p1.ID = v.PartMasterID
     * LEFT OUTER JOIN partmaster p2 on p2.ID = x.XREF_ID
     * WHERE P1.partnumber IN ('PDQ-001M') OR P2.partnumber IN ('PDQ-001M') ORDER BY v.MakeName
     */
    public function getVehicleInfoByPartNumberWithProtocol($partNumber, $params) {
        $this->getVehicles($partNumber);

        $this->addFieldToSelect('MakeName');
        $this->addFieldToSelect('ModelName');
        $this->addFieldToSelect('SubModelName');
        $this->addFieldToSelect('YearID');
        $this->addFieldToSelect('MakeID');
        $this->addFieldToSelect('ModelID');
        $this->addFieldToSelect('SubModelID');
        $this->addExpressionFieldToSelect('Date_PDQ', new \Zend_Db_Expr('group_concat(DISTINCT `p1`.`Date_PDQ` SEPARATOR ", ")'), []);
        $this->addExpressionFieldToSelect('Date_ATEQ', new \Zend_Db_Expr('group_concat(DISTINCT `p1`.`Date_ATEQ` SEPARATOR ", ")'), []);
        $this->addExpressionFieldToSelect('Date_Bartec', new \Zend_Db_Expr('group_concat(DISTINCT `p1`.`Date_Bartec` SEPARATOR ", ")'), []);
        $this->addExpressionFieldToSelect('LinkedDate_PDQ', new \Zend_Db_Expr('group_concat(DISTINCT `p2`.`Date_PDQ` SEPARATOR ", ")'), []);
        $this->addExpressionFieldToSelect('LinkedDate_ATEQ', new \Zend_Db_Expr('group_concat(DISTINCT `p2`.`Date_ATEQ` SEPARATOR ", ")'), []);
        $this->addExpressionFieldToSelect('LinkedDate_Bartec', new \Zend_Db_Expr('group_concat(DISTINCT `p2`.`Date_Bartec` SEPARATOR ", ")'), []);
        $this->getSelect()
                ->group(array('MakeName', 'ModelName', 'YearID'));
        $this->getSelect()->order(array('MakeName', 'ModelName', 'SubModelName', 'YearID DESC'));
        $this->addFieldToFilter(
                array('p1.partnumber', 'p2.partnumber'), array(
            array('eq' => $partNumber),
            array('eq' => $partNumber)
                )
        );
        if (!empty($params) && isset($params['year']) && isset($params['make']) && isset($params['model']) && isset($params['submodel'])) {

            if (intval($params['make']) > 0) {
                $this->addFieldToFilter(
                        array('main_table.MakeID'), array(
                    array('eq' => $params['make'])
                        )
                );
            }

            if (intval($params['model']) > 0) {
                $this->addFieldToFilter(
                        array('main_table.ModelID'), array(
                    array('eq' => $params['model'])
                        )
                );
            }

            if (intval($params['submodel']) > 0) {
                $this->addFieldToFilter(
                        array('main_table.SubModelID'), array(
                    array('eq' => $params['submodel'])
                        )
                );
            }

            if (intval($params['year']) > 0) {
                $this->addFieldToFilter(
                        array('YearID'), array(
                    array('eq' => $params['year'])
                        )
                );
            }
        }

        return $this;
    }

    /**
     * @param string $partNumber
     * @return $this
     */
    public function getVehicleInfoByWcPartNumber($partNumber) {
        $this->getVehicles();
        $this->addFieldToFilter(
                array('p1.partnumber', 'P2.partnumber'), array(
            array('like' => '%' . $partNumber . '%'),
            array('like' => '%' . $partNumber . '%'),
                )
        );

        return $this;
    }

    public function getSelectCountSql() {
        $countSelect = parent::getSelectCountSql();
        if (count($this->getSelect()->getPart(\Zend_Db_Select::GROUP)) > 0) {
            $countSelect->reset(\Zend_Db_Select::COLUMNS);
            $countSelect->reset(\Zend_Db_Select::GROUP);
            $group = $this->getSelect()->getPart(\Zend_Db_Select::GROUP);
            $countSelect->columns("COUNT(DISTINCT " . implode(", ", $group) . ")");
        }

        return $countSelect;
    }

}
