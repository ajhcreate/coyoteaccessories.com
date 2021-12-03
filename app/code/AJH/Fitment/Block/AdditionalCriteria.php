<?php

namespace AJH\Fitment\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\App\RequestInterface;
use AJH\Fitment\Model\Fitment;
use AJH\ProductVehicle\Model\VehiclestpmsFactory;

class AdditionalCriteria extends Template {
    
    protected $_template = 'AJH_Fitment::additionalcriteria.phtml';

    private $fitment;
    public $fitment_year, $fitment_make, $fitment_model, $fitment_submodel;
    protected $_vehicletpms;

    public function __construct(Context $context, Fitment $fitment,
            RequestInterface $request, VehiclestpmsFactory $vehicletpms) {

        $this->fitment_year = $request->getParam('year', false);
        $this->fitment_make = $request->getParam('make', false);
        $this->fitment_model = $request->getParam('model', false);
        $this->fitment_submodel = $request->getParam('submodel', false);

        $this->fitment = $fitment;

        $this->_vehicletpms = $vehicletpms;

        parent::__construct($context);
    }

    public function getAdditionalCriteria() {

        $collection = $this->_vehicletpms->create()->getCollection()
                ->addFieldToFilter('YearId', array('eq' => $this->fitment_year))
                ->addFieldToFilter('MakeID', array('eq' => $this->fitment_make))
                ->addFieldToFilter('ModelID', array('eq' => $this->fitment_model))
                ->addFieldToFilter('SubModelID', array('eq' => $this->fitment_submodel))
                ->addFieldToFilter('AdditionalCriteria_Question', array('notnull' => true))
                ->addFieldToFilter('AdditionalCriteria_PartMasterID', array('neq' => 0))
                ->addFieldToSelect('AdditionalCriteria_Question')
                ->addFieldToSelect('AdditionalCriteria_PartMasterID');
        $res = $collection->getFirstItem();

        return $res;
    }

}
