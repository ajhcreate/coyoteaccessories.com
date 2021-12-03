<?php

namespace AJH\Fitment\Controller\Adminhtml\Settings;

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

	die('Fitment Settings');

        $resultPage = $this->resultPageFactory->create();
//        $resultPage->setActiveMenu(static::MENU_ID);
        $resultPage->getConfig()->getTitle()->prepend(__('Fitment SEO/SEF URL Dashboard'));



        return $resultPage;
    }

    protected function _isAllowed() {
        return $this->_authorization->isAllowed('AJH_Fitment::dashboard');
    }

}
