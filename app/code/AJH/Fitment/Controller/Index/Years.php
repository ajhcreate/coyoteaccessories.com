<?php

namespace AJH\Fitment\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Years extends \Magento\Framework\App\Action\Action {

    protected $_pageFactory;
    protected $_resultPageFactory;

    public function __construct(Context $context, PageFactory $pageFactory) {
        $this->_resultPageFactory = $pageFactory;
        return parent::__construct($context);
    }

    public function execute() {

        $resultPage = $this->_resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__(' heading '));     
        
        /**
         *  AJH\Fitment\Observer\Caching
         */
        $this->_eventManager->dispatch('fitment_caching', ['params' => ['fitmentToCache' => 'years']]);    

        $block = $resultPage->getLayout()
                ->createBlock('AJH\Fitment\Block\Widget\Dropdown')
                ->setTemplate('AJH_Fitment::widget/dropdown/years.phtml')
                ->toHtml();
        $this->getResponse()->setBody($block);
    }

}
