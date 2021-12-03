<?php

namespace AJH\Customerfields\Observer;

use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;

class SaveBilling implements ObserverInterface {

    protected $logger;

    public function __construct(LoggerInterface$logger) {
        $this->logger = $logger;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {

        $order = $observer->getOrder();

        $shippingAddress = $order->getShippingAddress();
        $shippingAddress->setCustomerFulfillment('SWCA');
        $order->save();        

        return $this;
    }    

}
