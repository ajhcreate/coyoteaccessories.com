<?php

namespace AJH\FitmentUrlRewrite\Controller\Adminhtml\Ajax;

use Magento\Backend\App\Action;

class AddToSitemap extends Action {

    protected $resultJsonFactory;
    protected $_urlRewriteFactory;
    protected $_fitmentUrlRewriteModel;
    protected $_fitmentUrlRewriteFactory;
    protected $helper;
    protected $subject;
    protected $fitmentMakesFactory, $fitmentModelsFactory, $fitmentSubModelsFactory;
    protected $fitment_el = ['makes', 'models', 'submodels'];
    protected $_storemanager;

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

        $this->_storemanager = $storeManager;

        $this->helper = $helper;
        $this->subject = $subject;

        parent::__construct($context);
    }

    public function execute() {
                
        $params = $this->getRequest()->getParams();
        
        $year = $params['year'];        
        $factory = $this->_fitmentUrlRewriteFactory->create();
        $collection = $factory->getCollection()->addFieldToFilter('year', ['eq' => intval($year)]);

        $counter = 0;

        foreach ($collection as $_url) {
            $url = (object) $_url->getData();

            $make_alias = strtolower(str_replace(" ", "_", $url->make));
            $model_alias = strtolower(str_replace(" ", "_", $url->model));
            $submodel_alias = strtolower(str_replace(" ", "_", $url->submodel));

            $target_path = "fitment/index/categories/?year={$url->year}&make={$url->makeID}&model={$url->modelID}&submodel={$url->submodelID}&qualifiers[]=&_qualifiers[]=";
            $request_path = $url->request_path;

            $unique_id = "{$url->year}-{$make_alias}-{$model_alias}-{$submodel_alias}";

            $data = [
                "year" => $url->year,
                "makeID" => $url->makeID,
                "make" => $url->make,
                "modelID" => $url->modelID,
                "model" => $url->model,
                "submodelID" => $url->submodelID,
                "submodel" => $url->submodel,
                "target_path" => $target_path,
                "request_path" => $request_path,
                "unique_id" => $unique_id
            ];

            $added = $this->addUrlRewrite($data);

            $counter += intval($added);
        }

        $resultJson = $this->resultJsonFactory->create();

        return $resultJson->setData([
                    "count" => $counter
        ]);
    }

    public function addUrlRewrite($data) {

        $params = $this->getRequest()->getParams();
        $store = isset($params["request"]) ? $params["request"] : "coyoteaccessories";

        $store_id = $store === "pdqtpms" ? 4 : 1;

        $target_path = $data['target_path'];
        $request_path = $data['request_path'];

        if (!$this->isUrlRewritesExists($request_path, $store_id)) {

            $urlRewriteModel = $this->_urlRewriteFactory->create();
            /* set current store id */
            $urlRewriteModel->setStoreId($store_id);
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

            return 1;
        }

        return 0;
    }

    private function isUrlRewritesExists($requestUri, $store_id) {
        $urlrewrites = $this->_urlRewriteFactory->create();
        $collection = $urlrewrites->getCollection()->addFieldToFilter('request_path', ['eq' => $requestUri])->addFieldToFilter('store_id', ['eq' => $store_id]);
//        $collection = $urlrewrites->getCollection()->addFieldToFilter('store_id', ['eq' => 1]);

        return $collection->count() ? true : false;
    }

}
