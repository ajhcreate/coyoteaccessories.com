<?php

namespace AJH\Fitment\Controller\Adminhtml\System\Config;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;

class Cache extends Action {

    protected $resultJsonFactory;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param Data $helper
     */
    public function __construct(
    Context $context, JsonFactory $resultJsonFactory
    ) {
        $this->resultJsonFactory = $resultJsonFactory;        
        parent::__construct($context);
    }

    /**
     * Collect relations data
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute() {
        $response = null;
        
        try {
            $response = $this->_getFitmentCache()->clear();
        } catch (\Exception $e) {
            $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
        }
        
        /** @var \Magento\Framework\Controller\Result\Json $result */
        $result = $this->resultJsonFactory->create();

        return $result->setData(['success' => true, 'fitment' => $response]);
    }

    /**
     * Return product relation singleton
     *
     * @return \MageWorx\AlsoBought\Model\Relation
     */
    protected function _getFitmentCache() {
        return $this->_objectManager->get('AJH\Fitment\Model\System\Config\Cache');
    }

    protected function _isAllowed() {
        return $this->_authorization->isAllowed('AJH_Fitment::config');
    }

}

?>