<?php

namespace AJH\ProductVehicle\Controller\Search;

use Magento\Framework\App\Config\ScopeConfigInterface;

class All extends \Magento\Framework\App\Action\Action {

    protected $_pageFactory, $_helper, $_urlInterface, $_transportBuilder;
    protected $_scopeConfig;

    public function __construct(
    \Magento\Framework\App\Action\Context $context, \Magento\Framework\View\Result\PageFactory $pageFactory, Data $data, \Magento\Framework\UrlInterface $urlInterface, \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder, ScopeConfigInterface $scopeConfig) {
        $this->_pageFactory = $pageFactory;
        $this->_helper = $data;

        $this->_urlInterface = $urlInterface;
        $this->_transportBuilder = $transportBuilder;

        $this->_scopeConfig = $scopeConfig;

        return parent::__construct($context);
    }

    public function execute() {
        $resultPage = $this->_pageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__(' heading '));

        $block = $resultPage->getLayout()
                ->createBlock('AJH\ProductVehicle\Block\Product\View\FitmentList')
                ->toHtml();
        $this->getResponse()->setBody($block);
    }

}
