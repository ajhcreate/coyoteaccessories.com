<?php

namespace AJH\FitmentUrlRewrite\Controller\Adminhtml\Dashboard;

use Magento\Backend\App\Action;

class Index extends Action {
    
    const MENU_ID = 'AJH_FitmentUrlRewrite::dashboard';

    protected $resultPageFactory;
    protected $_urlRewriteFactory;

    public function __construct(\Magento\Backend\App\Action\Context $context,
            \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
            \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
            \Magento\Framework\View\LayoutFactory $layoutFactory,
            \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter,
            \Magento\Store\Model\StoreManagerInterface $storeManager,
            \Magento\Eav\Model\Config $eavConfig = null,
            \Magento\UrlRewrite\Model\UrlRewriteFactory $urlRewriteFactory,
            \Magento\Framework\View\Result\PageFactory $pageFactory) {

        $this->storeManager = $storeManager;
        $this->_urlRewriteFactory = $urlRewriteFactory;

        $this->resultPageFactory = $pageFactory;

        parent::__construct($context);
    }

//    public function ___construct(
//    \Magento\Framework\App\Action\Context $context,
//            \Magento\Framework\View\Result\PageFactory $pageFactory,
//            \Magento\UrlRewrite\Model\UrlRewriteFactory $urlRewriteFactory) {
//
//        $this->_pageFactory = $pageFactory;
//    }

    public function execute() {

        $resultPage = $this->resultPageFactory->create();
//        $resultPage->setActiveMenu(static::MENU_ID);
        $resultPage->getConfig()->getTitle()->prepend(__('Fitment SEO/SEF URL Dashboard'));
        
        

        return $resultPage;

//        $urlRewriteModel = $this->_urlRewriteFactory->create();
//        /* set current store id */
//        $urlRewriteModel->setStoreId(4);
//        /* this url is not created by system so set as 0 */
//        $urlRewriteModel->setIsSystem(0);
//        /* unique identifier - set random unique value to id path */
//        $urlRewriteModel->setIdPath(rand(1, 100000));
//        /* set actual url path to target path field */
//        $urlRewriteModel->setTargetPath("fitment/index/categories/?year=2020&make=16&model=5954&submodel=168&qualifiers[]=&_qualifiers[]=");
//        /* set requested path which you want to create */
//        $urlRewriteModel->setRequestPath("fitment/2020-alfa-romeo-giulia-quadrifoglio");
//        /* set current store id */
//        $urlRewriteModel->save();
//        return $this->_pageFactory->create();
    }

    protected function _isAllowed() {
        return $this->_authorization->isAllowed('AJH_FitmentUrlRewrite::dashboard');
    }

}
