<?php

namespace AJH\Orderbysku\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Framework\App\Action\Action {

    protected $_resultJsonFactory, $_resultPageFactory;

    public function __construct(Context $context,
            JsonFactory $resultJsonFactory, PageFactory $resultPageFactory) {
        parent::__construct($context);        

        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_resultPageFactory = $resultPageFactory;
    }

    public function execute() {
                
        $resultPageFactory = $this->_resultPageFactory->create();
        $resultPageFactory->getConfig()->getTitle()->set(__("Order By Sku"));

        /** @var \Magento\Theme\Block\Html\Breadcrumbs */
        $breadcrumbs = $resultPageFactory->getLayout()->getBlock('breadcrumbs');
        $breadcrumbs->addCrumb('home', [
            'label' => __('Home'),
            'title' => __('Home'),
            'link' => $this->_url->getUrl('')
                ]
        );
        $breadcrumbs->addCrumb('custom_module', [
            'label' => __('OrderBySku'),
            'title' => __('OrderBySku')
                ]
        );
        
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }

}
