<?php

namespace AJH\Fitment\Observer;

use AJH\Fitment\Model\Fitment;
use AJH\FitmentUrlRewrite\Model\SitemapFactory;
use Magento\Framework\Session\SessionManagerInterface as CoreSession;

class GenerateSEFUrl implements \Magento\Framework\Event\ObserverInterface {

    private $logger;
    private $_fitment;
    private $_coreSession;

    public function __construct(\Psr\Log\LoggerInterface $logger,
            Fitment $fitment, CoreSession $coreSession) {
        $this->logger = $logger;
        $this->_fitment = $fitment;
        $this->_coreSession = $coreSession;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
        
        return $this;
    }

}
