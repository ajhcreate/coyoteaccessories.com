<?php
/**
 * Deprecated: transferred to AJH\Fitment\Block\Catalog\Product\Fits.php
 */

namespace AJH\Fitment\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Session\SessionManagerInterface as CoreSession;

use AJH\Fitment\Model\Fitment;
use AJH\Fitment\Model\Fitment\VehiclePartsFactory;

class Product extends Template {

    protected $_coreSession;    
    protected $_fitment;
    protected $_vehicleParts;

    public function __construct(Context $context, CoreSession $coreSession, Fitment $fitment, VehiclePartsFactory $vehicleParts) {
        $this->_coreSession = $coreSession;
        
        $this->_vehicleParts = $vehicleParts;
        $this->_fitment = $fitment;

        parent::__construct($context);
    }

    public function isPartFits($_product) {
                
        $parts = $this->getVehicleParts($_product->getSku());
        
        return count($parts);
    }        


    public function getVehicleParts($partnumber){
//        $vehicle_filter = $this->_coreSession->getSelectedVehicle();                
        $vehicle_filter = $this->_fitment->currentlySelectedVehicle();    
        
        var_dump($vehicle_filter);
        
        $year = $vehicle_filter['year'];
        $make = $vehicle_filter['make'];
        $model = $vehicle_filter['model'];
        $submodel = $vehicle_filter['submodel'];
        
        $vehicleParts = $this->_vehicleParts->create();
        $collection = $vehicleParts->getCollection();
        $collection->addFieldToFilter('YearID', ['eq' => $year]);
        $collection->addFieldToFilter('MakeID', ['eq' => $make]);
        $collection->addFieldToFilter('ModelID', ['eq' => $model]);
        $collection->addFieldToFilter('SubModelID', ['eq' => $submodel]);
        $collection->addFieldToFilter('PartNumber', ['eq' => $partnumber]);

        return $collection;
        
    }

}
