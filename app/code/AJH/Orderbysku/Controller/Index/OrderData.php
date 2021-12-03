<?php

namespace AJH\Orderbysku\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;

class OrderData extends \Magento\Framework\App\Action\Action {

    private $_productCollection;
    protected $_request;
    protected $_productRepository;
    protected $_stockItemRepository;

    public function __construct(Context $context,
            ProductFactory $productCollection, HttpRequest $request,
            ProductRepositoryInterface $productRepository, StockRegistryInterface $stockItemRepository) {

        parent::__construct($context);

        $this->_productCollection = $productCollection;
        $this->_request = $request;
        $this->_productRepository = $productRepository;
        $this->_stockItemRepository = $stockItemRepository;
    }

    public function execute() {
        $response = [];
        $options = "";
        $isAjax = $this->getRequest()->isAjax();
        if ($isAjax) {
            $sku = $this->_request->getParam('ordersku');
            $_products = $this->_productCollection->create()->getCollection()                    
                    ->addAttributeToFilter('status', 1)
                    ->setVisibility(4)
                    ->addAttributeToFilter('sku', array('like' => $sku . '%'))->load();
            if (count($_products)) {
                foreach ($_products as $product) {
                    $optionsku = $product->getSku();
                    $productCollection = $this->_productRepository->get($optionsku);
                    $sku = $productCollection->getSku();
                    $optionname = $productCollection->getName();
                    $optionprice = $productCollection->getPrice();
                    $inner = $productCollection->getData("web_level_1_qty");
                    $master = $productCollection->getData("web_level_2_qty");
                    $qty_increment = $this->getStockItem($product->getId());
                    
                    $options .= "<a href=\"#\" id=\"product-{$product->getId()}\" class=\"product-item list-group-item list-group-item-action\" data-qtyIncrement=\"{$qty_increment->getMinSaleQty()}\" data-inner=\"{$inner}\" data-master=\"{$master}\" data-sku=\"$optionsku\">SKU: {$sku} | {$optionname} | Price: $"."{$optionprice}</a>";                    
                    $options .= "<span id=\"product-{$product->getId()}-name\" class=\"d-none product-name\"><strong>{$optionname}</strong></span>";
                }
                $response = ["data" => $options, "status" => "success"];
            } else {
                $response = ["data" => "", "status" => "failed"];                
            }

            $this->getResponse()->setHeader('Content-type', 'application/json');
            $this->getResponse()->setBody(json_encode(['outputHtml' => $response]));
        }      
    }
    
    private function getStockItem($productId){
        return $this->_stockItemRepository->getStockItem($productId);
    }

}
