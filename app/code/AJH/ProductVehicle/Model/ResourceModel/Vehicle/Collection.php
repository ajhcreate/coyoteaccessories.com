<?php
namespace AJH\ProductVehicle\Model\ResourceModel\Vehicle;

use AJH\ProductVehicle\Model\Vehicle as Model;
use AJH\ProductVehicle\Model\ResourceModel\Vehicle as ResourceModel;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection{
    
    public function _construct()    {
        $this->_init(Model::class, ResourceModel::class);
    }

    /** Add filter by part number
     * SELECT DISTINCT `Make` FROM `makes` INNER JOIN `models` ON models.MakesID=makes.ID INNER JOIN `applicationguide` ON applicationguide.ModelsID=models.ID INNER JOIN `partmaster` ON partmaster.PartClassID=applicationguide.PartClassID LIKE '%pdq-001%'
     *
     * @param string $partNumber
     * @return AJH_ProductVehicle_Model_ResourceModel_Vehicle_Collection
     */
    public function addFilterByPartNumber($partNumber)
    {
        $modelsTable = $this->getTable('models');
        $applicationGuideTable = $this->getTable('applicationguide');
        $partsTable = $this->getTable('partmaster');

        // join table to models
        $this->getSelect()->joinInner(
            array('models' => $modelsTable),
            'models.MakesID = main_table.ID',
            array('MakesID')
        );
        // join table to application guide
        $this->getSelect()->joinInner(
            array('applicationguide' => $applicationGuideTable),
            'applicationguide.ModelsID = models.ID',
            array('ModelsID')
        );
        // join table to parts table
        $this->getSelect()->joinInner(
            array('partmaster' => $partsTable),
            'partmaster.PartClassID=applicationguide.PartClassID',
            array('PartNumber')
        );
        $this->getSelect()->distinct(true);
        // filter by parts number
        $this->addFieldToFilter('PartNumber', array('eq' => $partNumber));
        //$this->addFieldToFilter('PartNumber', array('like' => '%'. $partNumber . '%'));
        $this->addFieldToSelect('Make');
        $this->getSelect()->group('Make');

        return $this;
    }
}