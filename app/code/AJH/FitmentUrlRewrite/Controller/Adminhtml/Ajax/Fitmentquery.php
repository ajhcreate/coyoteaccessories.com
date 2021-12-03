<?php

namespace AJH\FitmentUrlRewrite\Controller\Adminhtml\Ajax;

use Magento\Backend\App\Action;

class Fitmentquery extends Action {

    const MENU_ID = 'AJH_FitmentUrlRewrite::dashboard';

    protected $resultJsonFactory;
    protected $_urlRewriteFactory;
    protected $_fitmentUrlRewriteModel;
    protected $_fitmentUrlRewriteFactory;

    public function __construct(\Magento\Backend\App\Action\Context $context,
            \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
            \Magento\Store\Model\StoreManagerInterface $storeManager,
            \Magento\UrlRewrite\Model\UrlRewriteFactory $urlRewriteFactory,
            \AJH\FitmentUrlRewrite\Model\UrlRewrite $fitmentUrlRewriteModel,
            \AJH\FitmentUrlRewrite\Model\UrlRewriteFactory $fitmentUrlRewriteFactory) {

        $this->storeManager = $storeManager;
        $this->_urlRewriteFactory = $urlRewriteFactory;

        $this->_fitmentUrlRewriteFactory = $fitmentUrlRewriteFactory;
        $this->_fitmentUrlRewriteModel = $fitmentUrlRewriteModel;

        $this->resultJsonFactory = $resultJsonFactory;

        parent::__construct($context);
    }

    public function execute() {

        $params = $this->getRequest()->getParams();

        $i = $params['request'];

        switch ($i) {
            case 'year':
                $response = $this->loadYears();
                break;
            case 'make':
                $response = $this->loadMakes();
                break;
            case 'models':
                $response = $this->loadModels();
                break;
            case 'submodels':
                $response = $this->loadSubModels();
                break;
            default:
                $response = [];
        }

        $resultJson = $this->resultJsonFactory->create();

        return $resultJson->setData([
            'year' => $params['year'],
            'makeID' => isset($params['makeID'])?$params['makeID']:0,
            'makeName' => isset($params['makeName'])?$params['makeName']:NULL,
            'modelID' => isset($params['modelID'])?$params['modelID']:0,
            'modelName' => isset($params['modelName'])?$params['modelName']:NULL,
            'submodelID' => isset($params['submodelID'])?$params['submodelID']:0,
            'submodelName' => isset($params['submodelName'])?$params['submodelName']:NULL,
            'response' => json_encode($response),
            'messages' => 'Successfully. Params: ' . json_encode($params)
        ]);
    }

    public function loadYears() {
        $arr = [];
        $years = $this->_fitmentUrlRewriteModel->loadYears();

        foreach ($years as $year) {
            $data = $year->getData();
            $arr[] = $data['year'];
        }
        
        return $arr;
    }
    
    public function loadMakes(){
        $params = $this->getRequest()->getParams();
                
        $makes = $this->_fitmentUrlRewriteModel->loadMakes($params['year']);       
        
        return $makes;
    }
    
    public function loadModels(){
        $params = $this->getRequest()->getParams();
                
        $models = $this->_fitmentUrlRewriteModel->loadModels($params['year'], $params['makeID']);       
        
        return $models;
    }
    
    public function loadSubModels(){
        $params = $this->getRequest()->getParams();
                
        $submodels = $this->_fitmentUrlRewriteModel->loadSubModels($params['year'], $params['makeID'], $params['modelID']);       
        
        return $submodels;
    }

    protected function _isAllowed() {
        return true; //$this->_authorization->isAllowed('AJH_FitmentUrlRewrite::dashboard');
    }

}
