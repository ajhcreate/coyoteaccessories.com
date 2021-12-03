<?php

namespace AJH\FitmentUrlRewrite\Controller\Adminhtml\UrlRewrite;

use Magento\Backend\App\Action;

class Index extends Action {

    const MENU_ID = 'AJH_FitmentUrlRewrite::urlrewrite';

    protected $resultPageFactory;    

    public function __construct(\Magento\Backend\App\Action\Context $context,
            \Magento\Store\Model\StoreManagerInterface $storeManager,
            \Magento\Framework\View\Result\PageFactory $pageFactory) {

        $this->storeManager = $storeManager;

        $this->resultPageFactory = $pageFactory;

        parent::__construct($context);
    }

    public function execute() {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('Fitment SEF URL ReWrite'));

        return $resultPage;
    }

    protected function _isAllowed() {
        return $this->_authorization->isAllowed('AJH_FitmentUrlRewrite::rewrite');
    }

}
