<?php

namespace AJH\FitmentUrlRewrite\Controller\Adminhtml\Ajax;

use Magento\Backend\App\Action;

class SaveSitemap extends Action {

    protected $resultJsonFactory;
    protected $_urlRewriteFactory;
    protected $_fitmentUrlRewriteModel;
    protected $_fitmentUrlRewriteFactory;
    protected $helper;
    protected $subject;
    protected $fitmentMakesFactory, $fitmentModelsFactory, $fitmentSubModelsFactory;
    
    protected $fitment_el = ['makes', 'models', 'submodels'];

    public function __construct(\Magento\Backend\App\Action\Context $context,
            \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
            \Magento\Store\Model\StoreManagerInterface $storeManager,
            \Magento\UrlRewrite\Model\UrlRewriteFactory $urlRewriteFactory,
            \AJH\FitmentUrlRewrite\Model\SitemapFactory $fitmentUrlRewriteFactory,
            \AJH\Fitment\Model\MakesFactory $makesFactory,
            \AJH\Fitment\Model\ModelsFactory $modelsFactory,
            \AJH\Fitment\Model\SubModelsFactory $submodelsFactory,
            \Magento\Sitemap\Helper\Data $helper,
            \Magento\Sitemap\Model\Sitemap $subject) {

        $this->_fitmentUrlRewriteFactory = $fitmentUrlRewriteFactory;
        $this->_urlRewriteFactory = $urlRewriteFactory;
        $this->resultJsonFactory = $resultJsonFactory;

        $this->fitmentMakesFactory = $makesFactory;
        $this->fitmentModelsFactory = $modelsFactory;
        $this->fitmentSubModelsFactory = $submodelsFactory;

        $this->helper = $helper;
        $this->subject = $subject;

        parent::__construct($context);
    }

    public function execute() {

        $response = ["status" => 0];
        $params = $this->getRequest()->getParams();

        $make_alias = strtolower(str_replace(" ", "_", $params['makeName']));
        $model_alias = strtolower(str_replace(" ", "_", $params['modelName']));

        $target_path = null;
        $request_path = null;

        $submodels = json_decode($params['submodels']);
        $_submodelName = "";
        $_submodelID = 0;

        $resultJson = $this->resultJsonFactory->create();
        $model = $this->_fitmentUrlRewriteFactory->create();

        foreach ($submodels as $submodel) {
            $_submodelName = $submodel->SubModelName;
            $_submodelID = $submodel->SubModelID;

            $submodel_alias = strtolower(str_replace(" ", "_", $_submodelName));
            $target_path = "fitment/index/categories/?year={$params['year']}&make={$params['makeID']}&model={$params['modelID']}&submodel={$_submodelID}&qualifiers[]=&_qualifiers[]=";
            $request_path = "fitment/{$params['year']}-{$make_alias}-{$model_alias}-{$submodel_alias}";

            $unique_id = "{$params['year']}-{$make_alias}-{$model_alias}-{$submodel_alias}";

            if (!$this->isUrlRewritesExists($request_path)) {
                $model->addData([
                    "key" => $params['form_key'],
                    "year" => $params['year'],
                    "makeID" => $params['makeID'],
                    "make" => $params['makeName'],
                    "modelID" => $params['modelID'],
                    "model" => $params['modelName'],
                    "submodelID" => $_submodelID,
                    "submodel" => $_submodelName,
                    "target_path" => $target_path,
                    "request_path" => $request_path
                ]);
            }

            $saveData = $model->save();

            $response = ["status" => 0];
            $data = [];

            if ($saveData) {

                $data = [                    
                    "year" => $params['year'],
                    "makeID" => $params['makeID'],
                    "make" => $params['makeName'],
                    "modelID" => $params['modelID'],
                    "model" => $params['modelName'],
                    "submodelID" => $_submodelID,
                    "submodel" => $_submodelName,
                    "target_path" => $target_path,
                    "request_path" => $request_path,
                    "unique_id" => $unique_id
                ];

                $response = ["status" => 1];

                $this->addUrlRewrite($data);
                
                $this->saveFitmentData($data);
            }
        }

        return $resultJson->setData([
                    "year" => $params['year'],
                    "makeID" => $params['makeID'],
                    "make" => $params['makeName'],
                    "modelID" => $params['modelID'],
                    "model" => $params['modelName'],
                    "submodelID" => $_submodelID,
                    "submodel" => $_submodelName,
                    "target_path" => $target_path,
                    "request_path" => $request_path,
                    "response" => json_encode($response),
                    "messages" => 'Successfully. Params: ' . json_encode($params),
                    "error" => false
        ]);
    }

    public function addUrlRewrite($data) {

        $target_path = $data['target_path'];
        $request_path = $data['request_path'];

        if (!$this->isUrlRewritesExists($request_path)) {

//            $this->deleteUrlRewrites($request_path);

            $urlRewriteModel = $this->_urlRewriteFactory->create();
            /* set current store id */
            $urlRewriteModel->setStoreId(4);
            /* this url is not created by system so set as 0 */
            $urlRewriteModel->setIsSystem(0);
            /* unique identifier - set random unique value to id path */
            $urlRewriteModel->setIdPath(rand(1, 100000));
            /* set actual url path to target path field */
            $urlRewriteModel->setTargetPath($target_path);
            /* set requested path which you want to create */
            $urlRewriteModel->setRequestPath($request_path);
            /* set current store id */
            $urlRewriteModel->save();
        }
    }

    public function addToSitemap($data) {

        $request_path = $data['request_path'];
        $unique_id = $data['unique_id'];

        $storeId = 4; //$this->subject->getStoreId();
        $newRecords = [];
        $object = new \Magento\Framework\DataObject();
        $object->setId([$unique_id]);
        $object->setUrl($request_path);
        $object->setUpdatedAt(date("Y-m-d H:m:s"));

        $newRecords[$unique_id] = $object;

        $this->subject->addSitemapItem(new \Magento\Framework\DataObject(
                [
            'changefreq' => $this->helper->getPageChangefreq($storeId),
            'priority' => $this->helper->getPagePriority($storeId),
            'collection' => $newRecords,
                ]
        ));
    }

    private function isUrlRewritesExists($requestUri) {
        $urlrewrites = $this->_urlRewriteFactory->create();
        $collection = $urlrewrites->getCollection()->addFieldToFilter('request_path', ['eq' => $requestUri]);

        return $collection->count() ? true : false;
    }

    private function deleteUrlRewrites($requestUri) {

        $urlrewrites = $this->_fitmentUrlRewriteFactory->create();
        $collection = $urlrewrites->getCollection()->addFieldToFilter('request_path', ['eq' => $requestUri]);

        $deleteItem = $collection->getFirstItem();
        if ($collection->getFirstItem()->getId()) {
            // target path does exist
            $deleteItem->delete();
        }
    }

    private function saveFitmentData($data) {
        
        foreach($this->fitment_el as $fitment){
            switch ($fitment) {
                case 'makes':
                    $fitmentFactory = $this->fitmentMakesFactory->create();
                    $data['fitment'] = 'makes';
                    break;
                case 'models':
                    $fitmentFactory = $this->fitmentModelsFactory->create();
                    $data['fitment'] = 'models';
                    break;
                case 'submodels':
                    $fitmentFactory = $this->fitmentSubModelsFactory->create();
                    $data['fitment'] = 'submodels';
                    break;
            }     
            
            $data['makeName'] = $data['make'];
            $data['modelName'] = $data['model'];
            $data['submodelName'] = $data['submodel'];
            
            if(!$this->fitmentDataExists($data, $fitmentFactory)){                
                $fitmentFactory->addData($data);
                $fitmentFactory->save();
            }
            
        }        
        
        return TRUE;
    }

    private function fitmentDataExists($data, $fitmentFactory) {
        
        $fitment = $data['fitment'];
        $year = $data['year'];
        $makeID = $data['makeID'];
        $modelID = $data['modelID'];
        $submodelID = $data['submodelID'];
        
        $collection = $fitmentFactory->getCollection();
        
        switch ($fitment) {
            case 'makes':
                $collection->addFieldToFilter('year', ['eq' => $year]);
                $collection->addFieldToFilter('makeID', ['eq' => $makeID]);
                break;
            case 'models':
                $collection->addFieldToFilter('year', ['eq' => $year]);
                $collection->addFieldToFilter('makeID', ['eq' => $makeID]);
                $collection->addFieldToFilter('modelID', ['eq' => $modelID]);
                break;
            case 'submodels':
                $collection->addFieldToFilter('year', ['eq' => $year]);
                $collection->addFieldToFilter('makeID', ['eq' => $makeID]);
                $collection->addFieldToFilter('modelID', ['eq' => $modelID]);
                $collection->addFieldToFilter('submodelID', ['eq' => $submodelID]);
                break;
        }
        
        return $collection->count() > 0?TRUE:FALSE;
        
    }

}
