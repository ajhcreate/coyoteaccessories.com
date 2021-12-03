<?php

namespace AJH\FitmentUrlRewrite\Controller\Adminhtml\Ajax;

use Magento\Backend\App\Action;

class SaveModels extends Action {

    protected $resultJsonFactory;
    protected $_urlRewriteFactory;
    protected $_fitmentUrlRewriteModel;
    protected $_fitmentUrlRewriteFactory;

    public function __construct(\Magento\Backend\App\Action\Context $context,
            \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
            \Magento\Store\Model\StoreManagerInterface $storeManager,
            \AJH\FitmentUrlRewrite\Model\Models $fitmentUrlRewriteModel,
            \AJH\FitmentUrlRewrite\Model\ModelsFactory $fitmentUrlRewriteFactory) {

        $this->storeManager = $storeManager;

        $this->_fitmentUrlRewriteFactory = $fitmentUrlRewriteFactory;
        $this->_fitmentUrlRewriteModel = $fitmentUrlRewriteModel;

        $this->resultJsonFactory = $resultJsonFactory;

        parent::__construct($context);
    }

    public function execute() {

        $response = ["status" => 0];
        $params = $this->getRequest()->getParams();

        $resultJson = $this->resultJsonFactory->create();

        $model = $this->_fitmentUrlRewriteFactory->create();
        $model->addData([
            "key" => $params['form_key'],
            "year" => $params['year'],
            "makeID" => $params['makeID'],
            "make" => $params['makeName'],
            "models" => $params['models'],
            "completed" => 0
        ]);

        $saveData = $model->save();
        if ($saveData) {
            $response = ["status" => 1];
        }

        return $resultJson->setData([
                    'year' => $params['year'],
                    'makeID' => $params['makeID'],
                    'makeName' => $params['makeName'],
                    'models' => $params['models'],
                    'response' => json_encode($response),
                    'messages' => 'Successfully. Params: ' . json_encode($params),
                    'error' => false
        ]);
    }

}
