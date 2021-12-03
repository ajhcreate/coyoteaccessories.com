<?php

namespace AJH\InvoiceProcessor\Observer;

use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Sales\Model\Order\Item;

class SalesOrderInvoiceSaveBefore implements ObserverInterface {

    protected $logger, $_resourceConnection, $_productRepository, $_orderItem;    

    public function __construct(LoggerInterface$logger, ResourceConnection $resourceConnection, ProductRepositoryInterface $productRepository, Item $orderItem) {
        $this->logger = $logger;
        $this->_resourceConnection = $resourceConnection;
        $this->_productRepository = $productRepository;
        $this->_orderItem = $orderItem;        
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
        try {
            $invoice = $observer->getEvent()->getInvoice()->getOrder();

            echo 'Shipping Amount: ' . $invoice->getPayment()->getShippingAmount() . "<br/>";

            foreach ($invoice->getAllItems() as $item) {
                /* Draw item */
                $name = $item->getName();

                echo 'Item ' . $name . ' Price -' . $item->getPrice() . "<br/>";
            }
            die;
        } catch (\Exception $e) {
            $this->logger->info($e->getMessage());
        }
    }

}
