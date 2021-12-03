<?php

namespace AJH\ShippingBox\Observer;

use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Sales\Model\Order\Item;
use Magento\Sales\Model\Order\ItemFactory;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Registry;

class Orderplaceafter implements ObserverInterface {

    protected $logger, $_resourceConnection, $_productRepository, $_orderItem, $_logger, $_objectmanager;
    private $box_skus, $product_skus, $orderRepository, $orderItemFactory, $processed_flag = false;
    protected $_registry;

    public function __construct(LoggerInterface$logger,
            ResourceConnection $resourceConnection,
            ProductRepositoryInterface $productRepository, Item $orderItem,
            ItemFactory $orderItemFactory,
            OrderRepositoryInterface $orderRepository,
            ObjectManagerInterface $objectmanager, Registry $registry) {
        $this->logger = $logger;
        $this->_resourceConnection = $resourceConnection;
        $this->_productRepository = $productRepository;
        $this->_orderItem = $orderItem;
        $this->orderItemFactory = $orderItemFactory;
        $this->box_skus = array();
        $this->product_skus = array();
        $this->_objectManager = $objectmanager;
        $this->orderRepository = $orderRepository;

        $this->_registry = $registry;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
        $event = $observer->getEvent();

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/webgility.log');
        $logger = new \Zend\Log\Logger();
        $this->_logger = $logger;
        $logger->addWriter($writer);

//        $logger->info('Event Name: ' . $event->getName());

        $order = $observer->getOrder();

        $_registry = 'prevent_duplicate_'.$order->getId();

        if (!$this->_registry->registry($_registry)):
            $this->_registry->register($_registry, TRUE);

            $logger->info('Log entry point!');
            $logger->info('Order Status:' . $order->getState());

            try {

                $_payment = $order->getPayment();
                $payment = $_payment->toArray();
                $store_id = $order->getStore()->getStoreId();

//            $json_payment = json_encode($payment);
                $logger->info('Order ID: ' . $order->getId());
                $logger->info('Store ID: ' . $store_id);
                $logger->info('Store Name: ' . $order->getStore()->getName());
                $logger->info('Store Code: ' . $order->getStore()->getCode());

                $payment_additional_info = $payment['additional_information'];

                if (isset($payment_additional_info['component_mode'])) {
                    $logger->info('Component Mode: ' . strtolower($payment_additional_info['component_mode']));
                }

//        $increment_id_prefix = substr($orders['increment_id'], 0, 2);                
//        if ($increment_id_prefix === 'WP' && isset($payment_additional_info['component_mode']) && strtolower($payment_additional_info['component_mode']) === 'ebay' && isset($payment['method']) && strtolower(trim($payment['method'])) === 'm2epropayment') {
                //store id 5 = Wheel Accessories store
                if (strtolower($order->getState()) === 'new' && intval($store_id) === 5 && isset($payment_additional_info['component_mode']) && strtolower($payment_additional_info['component_mode']) === 'ebay' && isset($payment['method'])) {

                    $items = $order->getAllVisibleItems();

//get the order product skus
                    foreach ($items as $k => $item) {
                        $sku = $item->getSku();
                        $logger->info('Item Status: ' . $item->getStatus());                        

                        $connection = $this->_objectManager->get('\Magento\Framework\App\ResourceConnection')->getConnection();
//                    $sku = '1012BOW.2';
                        $productId = 11987;
                        $productQty = 1;

                        $sql = "SELECT `box_sku`, `price` FROM `product_box_sku_price` WHERE `product_sku`='{$sku}'";
                        $boxes = $connection->fetchAll($sql);

                        if (count($boxes)) {
                            foreach ($boxes as $box) {
                                $boxItem = $this->orderItemFactory->create();
                                $boxItem->setProductId($productId);
                                $boxItem->setSku($box['box_sku']);
                                $boxItem->setName($box['box_sku']);
                                $boxItem->setDescription('Box: ' . $box['box_sku']);
                                $boxItem->setQtyOrdered($productQty);
                                $boxItem->setOriginalPrice($box['price']);
                                $boxItem->setPrice($box['price']);
                                $boxItem->setWeight('');
                                $boxItem->setFreeShipping("N");
                                $boxItem->setshippingFreight("0.00");
                                $boxItem->setWeight_Symbol("lbs");
                                $boxItem->setWeight_Symbol_Grams("0.001");
                                $boxItem->setDiscounted("Y");
                                $boxItem->setDiscountAmount($box['price']);
                                $boxItem->setRowTotal($box['price']);
                                $order->addItem($boxItem);
                                $this->orderRepository->save($order);
                            }
                        }
//                    endif;
                    }
                }
            } catch (\Exception $e) {
                $this->logger->info($e->getMessage());
                $logger->info('OrderPlaceAfter Error: ' . $e->getMessage());
            }

        endif;
    }

}
