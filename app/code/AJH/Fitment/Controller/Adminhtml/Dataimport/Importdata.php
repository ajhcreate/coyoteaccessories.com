<?php

namespace AJH\Fitment\Controller\Adminhtml\Dataimport;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\PageFactory;

class Importdata extends \Magento\Backend\App\Action
{
    private $coreRegistry;
    protected $resultPageFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
            PageFactory $pageFactory
    ) {                                 

       parent::__construct($context);
       $this->coreRegistry = $coreRegistry;
       $this->resultPageFactory = $pageFactory;
    }

    public function execute()
    {
        $rowData = $this->_objectManager->create('AJH\Fitment\Model\Fitment\VehicleParts');
        $this->coreRegistry->register('fitment_data', $rowData);
//        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
//        $resultPage->getConfig()->getTitle()->prepend(__('Import Locator Data'));
        
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('Import Fitment Data'));

        return $resultPage;        
    }

    // used for acl.xml
    protected function _isAllowed()   {
        return $this->_authorization->isAllowed('AJH_Fitment::dataimport');
    }
}