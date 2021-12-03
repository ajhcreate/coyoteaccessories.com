<?php

namespace AJH\FitmentUrlRewrite\Controller\Adminhtml\Ajax;

use Magento\Backend\App\Action;

class Index extends Action {

    const MENU_ID = 'AJH_FitmentUrlRewrite::ajax';

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

        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('Fitment SEF URL Rewrite'));

        return $resultPage;
    }

}
