<?php

namespace AJH\ProductVehicle\Controller\Fitment;

use Magento\Framework\App\Config\ScopeConfigInterface;

class Makes extends \Magento\Framework\App\Action\Action {

    protected $_pageFactory, $_helper, $_urlInterface, $_transportBuilder;
    protected $_scopeConfig;

    public function __construct(
    \Magento\Framework\App\Action\Context $context,
            \Magento\Framework\View\Result\PageFactory $pageFactory,
            \Magento\Framework\UrlInterface $urlInterface,
            \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
            ScopeConfigInterface $scopeConfig) {
        $this->_pageFactory = $pageFactory;        

        $this->_urlInterface = $urlInterface;
        $this->_transportBuilder = $transportBuilder;

        $this->_scopeConfig = $scopeConfig;

        return parent::__construct($context);
    }

    public function execute() {                
        $resultPage = $this->_pageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__(' heading '));

        $block = $resultPage->getLayout()
                ->createBlock('AJH\ProductVehicle\Block\Fitment\Items')                                
                ->toHtml();
        $this->getResponse()->setBody($block);
    }

}
