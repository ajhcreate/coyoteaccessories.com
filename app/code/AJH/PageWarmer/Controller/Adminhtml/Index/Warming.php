<?php

namespace AJH\PageWarmer\Controller\Adminhtml\Index;

class Warming extends \Magento\Framework\App\Action\Action {

    protected $resultRawFactory;

    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $layoutFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     */
    public function __construct(
    \Magento\Backend\App\Action\Context $context,
            \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
            \Magento\Framework\View\LayoutFactory $layoutFactory,
            \Magento\Framework\Registry $registry
    ) {
        parent::__construct($context);
        $this->resultRawFactory = $resultRawFactory;
        $this->layoutFactory = $layoutFactory;
        $this->_coreRegistry = $registry;
    }

    public function execute() {

        $resultRaw = $this->resultRawFactory->create();
        return $resultRaw->setContents(
                        $this->layoutFactory->create()->createBlock(
                                'AJH\PageWarmer\Block\Adminhtml\Index\Warming', 'pagewarmer.fpc.grid'
                        )->toHtml()
        );
    }

}
