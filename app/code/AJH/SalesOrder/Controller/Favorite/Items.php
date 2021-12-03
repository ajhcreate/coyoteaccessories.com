<?php

namespace AJH\SalesOrder\Controller\Favorite;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Customer\Model\Session as CustomerSession;

class Items extends \Magento\Framework\App\Action\Action {

    protected $_pageFactory, $_helper, $_urlInterface, $_transportBuilder, $http;
    protected $_scopeConfig, $_customerSession;

    public function __construct(
    \Magento\Framework\App\Action\Context $context,
            \Magento\Framework\View\Result\PageFactory $pageFactory,
            \Magento\Framework\UrlInterface $urlInterface,
            \Magento\Framework\App\Response\Http $http,
            \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
            ScopeConfigInterface $scopeConfig, CustomerSession $customerSession) {

        $this->_pageFactory = $pageFactory;
        $this->_urlInterface = $urlInterface;
        $this->_transportBuilder = $transportBuilder;
        $this->_customerSession = $customerSession;
        $this->http = $http;
        $this->_scopeConfig = $scopeConfig;

        return parent::__construct($context);
    }

    public function execute() {
//        $resultPage = $this->_pageFactory->create();
//        $resultPage->getConfig()->getTitle()->prepend(__(' heading '));
//
//        $block = $resultPage->getLayout()
//                ->createBlock('AJH\SalesOrder\Block\Favorite\Items')
//                ->toHtml();
//        $this->getResponse()->setBody($block);

        /**
         * Check if user logged in
         */
        if (!$this->_customerSession->isLoggedIn()) {
            $this->http->setRedirect($this->_urlInterface->getUrl('customer/account/login'), 301);         
        }        

        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }

}
