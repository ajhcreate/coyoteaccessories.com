<?php

namespace AJH\FitmentUrlRewrite\Block\Adminhtml;

use Magento\Backend\Block\Template;
use Magento\Framework\Module\Manager;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\App\ResourceConnection;

use AJH\Fitment\Model\YearsFactory;
use AJH\Fitment\Model\MakesFactory;
use AJH\FitmentUrlRewrite\Model\UrlRewrite;

use AJH\FitmentUrlRewrite\Model\UrlRewriteFactory;

class Dashboard extends Template {

    protected $_yearsCollection;
    protected $_makesCollection;
    protected $_urlRewriteFactory;
    protected $_urlRewriteModel;
    protected $_resource;
    protected $_connection;

    public function __construct(
    Context $context, YearsFactory $yearsCollection,
            MakesFactory $makesCollection, UrlRewrite $urlRewrite,
            UrlRewriteFactory $urlRewriteFactory, ResourceConnection $resource,
            array $data = []
    ) {
        $this->_yearsCollection = $yearsCollection;

        $this->_makesCollection = $makesCollection;

        $this->_urlRewriteModel = $urlRewrite;

        $this->_urlRewriteFactory = $urlRewriteFactory;

        $this->_resource = $resource;
        $this->_connection = $resource->getConnection();

        parent::__construct($context, $data);
    }
    
    public function getUrlCollection(){
        
        $fitment = $this->_urlRewriteFactory->create();
        
        $collection = $fitment->getCollection();
        
        
        return $collection;
    }

    public function getYearsCollection() {
        $years = $this->_yearsCollection->create();
        $collection = $years->getCollection();

        return $collection;
    }

    public function getMakesCollection($year) {        

        $makes = $this->_urlRewriteModel->loadMakes($year);

        return $makes;
    }
    
    public function getModelsCollection($year, $make) {

        $models = $this->_urlRewriteModel->loadModels($year, $make);

        return $models;
    }
    
    public function getSubModelsCollection($year, $make, $model) {

        $submodels = $this->_urlRewriteModel->loadSubModels($year, $make, $model);

        return $submodels;
    }
    
    public function getQualifiersCollection($year, $make, $model, $submodel) {

        $qualifiers = $this->_urlRewriteModel->loadQualifiers($year, $make, $model, $submodel);

        return $qualifiers;
    }
    
    public function loadFitmentCollection(){
        $year = 2021;
        
        
//        foreach($makes as $make){
                        
            $makes = $this->getMakesCollection($year);
            $models = $this->getModelsCollection($year, $makes);
            $submodels = $this->getSubModelsCollection($year, $models);
            $qualifiers = $this->getQualifiersCollection($year, $models);
            
            var_dump($models);
            die('--end--');
//        }
    }

    public function insertMultiple($table, $data) {
        try {
            $tableName = $this->_resource->getTableName($table);
            return $this->_connection->insertMultiple($tableName, $data);
        } catch (\Exception $e) {
            echo $e;
        }
    }

    public function saveFitmentData() {
        try {
            $yourData = [
                ['year' => 2020, 'make' => 22, 'make_name' => 'test', 'model' => 22, 'model_name' => 'test', 'submodel' => 22, 'submodel_name' => 'test', 'qualifier' => '', 'url_key' => 'sample-sample']
            ];

            $tableName = 'api_fitment_sitemap';
            $this->insertMultiple($tableName, $yourData);

            echo __('Successfully inserted data.');
        } catch (Exception $e) {
            echo __('Cannot save data.');
        }
    }
    
    /**
     * Return ajax url for collect button
     *
     * @return string
     */
    public function getAjaxUrl($controller=null){
        if(!$controller){
            return $this->getUrl('fitmentseo/ajax/fitmentquery');
        }else{
            return $this->getUrl('fitmentseo/ajax/'.$controller);
        }
    }

}
