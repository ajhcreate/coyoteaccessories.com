<?php

namespace AJH\SalesOrder\Observer;

use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Sales\Model\Order\Item;

class SalesOrder implements ObserverInterface {

    protected $logger, $_resourceConnection, $_productRepository;    

    public function __construct(LoggerInterface $logger, ResourceConnection $resourceConnection, ProductRepositoryInterface $productRepository) {
        $this->logger = $logger;
        $this->_resourceConnection = $resourceConnection;
        $this->_productRepository = $productRepository;        
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
        try {
//            $order = $observer->getEvent()->getOrder();
//            $quote = $order->getQuote();
//
//            $resource = $this->_resourceConnection->getConnection('core_write');
//            $writeConnection = $resource->getConnection('core_write');
//            $table = $resource->getTableName('sales_order_item');
//
//            foreach ($order->getAllVisibleItems() as $item) {
//                $product = $this->getProductById($item->getProductId());
//
//                $_originalPrice = $item->getOriginalPrice();
//                $_price = $product->getPrice();
//                $_price_fraction = $_price - floor($_price);
//                $_fraction = $_originalPrice - floor($_originalPrice);
//
//                $price_fraction = ceil((number_format($_price_fraction, 4) * 10000) / 100) * 100;
//                $price = $_price_fraction > 0 ? floor($_price) + ($price_fraction / 10000) : $_price;
//
//                $fraction = ceil((number_format($_fraction, 4) * 10000) / 100) * 100;
//                $original_price = $_fraction > 0 ? floor($_originalPrice) + ($fraction / 10000) : $_originalPrice;
//
//                $query = "UPDATE {$table} SET price = '{$price}', base_price = '{$price}', original_price = '{$original_price}', base_original_price = '{$original_price}' WHERE quote_item_id = " . (int) $item->getId();
//                $writeConnection->query($query);
//            }
        } catch (\Exception $e) {
            $this->logger->info($e->getMessage());
        }
    }

    private function getProductById($id) {
        return $this->_productRepository->getById($id);
    }

    public function loadMyProduct($sku) {
        return $this->_productRepository->get($sku);
    }

}
