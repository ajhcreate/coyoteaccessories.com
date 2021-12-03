<?php

namespace AJH\PageWarmer\Block\Adminhtml;

use AJH\PageWarmer\Model\Fitment;

class Queue extends \Magento\Backend\Block\Template {

    protected $_template = 'AJH_PageWarmer::queue/logs.phtml';
    protected $collection;
    protected $fitment;

    public function __construct(
    \Magento\Backend\Block\Template\Context $context,
            \AJH\PageWarmer\Model\ResourceModel\Queue\CollectionFactory $collection,
            Fitment $fitment
    ) {
        parent::__construct($context);
        $this->collection = $collection;
        
        $this->fitment = $fitment;
    }

    /**
     * @return void
     */
    protected function _construct() {
        $this->_blockGroup = 'AJH_PageWarmer';
        $this->_controller = 'adminhtml_queue_index';
        $this->_headerText = __('Queue');
        $this->_addButtonLabel = __('OK');
        parent::_construct();
    }
    
    public function getCollection() {
        $collection = $this->collection->create();

        return $collection;
    }
    
    public function getYearsCollection(){
        $years = $this->fitment->getYears();
        
        return $years;
    }
    
    public function getMakesCollection($year){
        $makes = $this->fitment->getMakes($year);
        
        return $makes;
    }
    
    public function getModelsCollection($year, $make){
        $models = $this->fitment->getModels($year, $make);
        
        return $models;
    }
    
    public function getSubModelsCollection($year, $make, $model){
        $submodels = $this->fitment->getModels($year, $make, $model);
        
        return $submodels;
    }
    
    public function getFitmetUrls($fitment=[]){
//        $url = $this->fitment->getFitmentUrls($fitment);
        
        return $this->fitment->generateFitmentUrls();
    }
    
    public function processQueue(){
        $years = $this->fitment->getYears();                
        
        foreach($years as $year){
            $_year_arr = $year->getData();
            
            $makes = $this->fitment->getMakes($_year_arr["year"]);
            
            echo 'Size: '.$makes->getSize();
        }
        
    }

}
