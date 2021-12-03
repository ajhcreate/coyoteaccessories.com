<?php

namespace AJH\Customerfields\Observer;

use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;

class SalesOrderPlaceAfter implements ObserverInterface {

    protected $logger;

    public function __construct(LoggerInterface$logger) {
        $this->logger = $logger;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
        $order = $observer->getOrder();
        $warehouse = 'SWKC';

//        $this->logger->info('$order->getCustomerId() = ' . $order->getCustomerId());

        if ($order->getCustomerId()) {
            $customerId = $order->getCustomerId();
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $customerObj = $objectManager->create('Magento\Customer\Model\Customer')->load($customerId);
            $customerAddress = array();

            foreach ($customerObj->getAddresses() as $address) {
                $customerAddress[] = $address->toArray();
            }

            foreach ($customerAddress as $customerAddres) {
//                $this->logger->info('$customerAddress');
//                $this->logger->info(serialize($customerAddres));
                if (isset($customerAddres['customer_address_fulfillment']) && $customerAddres['customer_address_fulfillment'] != '') {
                    $warehouse = $customerAddres['customer_address_fulfillment'];
                }
            }
        }

        $shippingAddress = $order->getShippingAddress();
        $shippingAddress->setCustomerAddressFulfillment($warehouse);
        $order->save();

        return $this;
    }

}
