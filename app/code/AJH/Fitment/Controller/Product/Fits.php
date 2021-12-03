<?php

namespace AJH\Fitment\Controller\Product;

class Fits extends \Magento\Framework\App\Action\Action {

    protected $_pageFactory;

    public function __construct(
            \Magento\Framework\App\Action\Context $context,
            \Magento\Framework\View\Result\PageFactory $pageFactory) {
        $this->_pageFactory = $pageFactory;
        return parent::__construct($context);
    }

    public function execute() {
        $resultPage = $this->_pageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__(' heading '));

        $block = $resultPage->getLayout()
                ->createBlock('AJH\Fitment\Block\Product\Fits');
        $fits = $block->isPartFits(); //@var $fits json data
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody($fits);
    }

}
