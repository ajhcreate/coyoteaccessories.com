<?php

namespace AJH\Fitment\Controller\Adminhtml\Dashboard;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action {

    const MENU_ID = 'AJH_FitmentUrlRewrite::dashboard';

    protected $resultPageFactory;
    protected $_urlRewriteFactory;

    public function __construct(Context $context, PageFactory $pageFactory) {        

        $this->resultPageFactory = $pageFactory;

        parent::__construct($context);
    }

    public function execute() {

        $resultPage = $this->resultPageFactory->create();
//        $resultPage->setActiveMenu(static::MENU_ID);
        $resultPage->getConfig()->getTitle()->prepend(__('Fitment Dashboard'));

        return $resultPage;
    }

    protected function _isAllowed() {
        return $this->_authorization->isAllowed('AJH_Fitment::dashboard');
    }

}
