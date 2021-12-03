<?php

namespace AJH\ProductVehicle\Controller\Search;

use Magento\Framework\App\Config\ScopeConfigInterface;
//use Magento\CatalogSearch\Model\Session as CatalogSearchSession;
use Magento\Framework\Session\Generic as CatalogSearchSession;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\UrlInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\StoreManagerInterface;

class Byparts extends \Magento\Framework\App\Action\Action {

    protected $_pageFactory, $_helper, $_urlInterface, $_transportBuilder;
    protected $_scopeConfig, $_catalogSearchSession, $_storeManager;

    public function __construct(
    Context $context, PageFactory $pageFactory, Data $data, UrlInterface $urlInterface, TransportBuilder $transportBuilder, ScopeConfigInterface $scopeConfig, CatalogSearchSession $catalogSearchSession, StoreManagerInterface $storeManager) {
        $this->_pageFactory = $pageFactory;
        $this->_helper = $data;

        $this->_urlInterface = $urlInterface;
        $this->_transportBuilder = $transportBuilder;

        $this->_scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;

        $this->_catalogSearchSession = $catalogSearchSession;

        return parent::__construct($context);
    }

    public function execute() {
        $resultPage = $this->_pageFactory->create();                

//        try {
//            
//        } catch (\Exception $e) {
//            $this->_catalogSearchSession->addError($e->getMessage());
//            $this->_redirectError(
//                    $this->_storeManager
//                            ->setQueryParams($this->getRequest()->getQuery())
//                            ->getUrl('*')
//            );
//        }
        
        
//        $title = printf("Search results for: '%s'", '123');
//        $resultPage->getLayout()->getBlock('head')->setTitle($title);
        
//        $resultPage->getConfig()->getTitle()->set(__($title));

        if ($breadcrumbs = $resultPage->getLayout()->getBlock('breadcrumbs')) {
            $breadcrumbs->addCrumb('home', array(
                'label' => __('Home'),
                'title' => __('Go to Home Page'),
                'link' => $this->_urlInterface->getBaseUrl()
            ))->addCrumb('search', array(
                'label' => __('Search by Parts'),
                'link'=>$this->_urlInterface->getUrl('*')
            ))->addCrumb('search_result', array(
                'label' => __('Results')
            ));
        }                
        
        
//        $this->_initLayoutMessages('catalog/session');
//        $this->renderLayout();
        return $resultPage;
    }

}
