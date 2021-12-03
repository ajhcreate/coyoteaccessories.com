<?php

namespace AJH\Fitment\Observer;

use AJH\Fitment\Model\Fitment;
use AJH\Fitment\Model\Cache as FitmentCache;
use Magento\Framework\Session\SessionManagerInterface as CoreSession;

class Caching implements \Magento\Framework\Event\ObserverInterface {

    private $logger;
    private $_fitment;
    private $_coreSession;
    private $_fitmentCache;

    public function __construct(\Psr\Log\LoggerInterface $logger,
            Fitment $fitment, CoreSession $coreSession,
            FitmentCache $fitmentCache) {
        $this->logger = $logger;
        $this->_fitment = $fitment;
        $this->_coreSession = $coreSession;
        $this->_fitmentCache = $fitmentCache;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {

        $cached_vehicles = [];
        $this->_fitmentCache;
                 
         $params = $observer->getData('params');
         
         $this->_fitmentCache->cacheFitmentParams($params);
         
             
        $this->logger->info('Fitment Caching', ['data' => $cached_vehicles]);

        return $this;
    }

}
