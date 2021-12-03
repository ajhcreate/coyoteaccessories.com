<?php

namespace AJH\Orderbysku\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Checkout\Model\Cart as CheckoutCart;
use Magento\Framework\App\ResponseFactory;
use Magento\Framework\UrlInterface;

class AddCart extends \Magento\Framework\App\Action\Action {

    private $_productCollection;
    protected $_request;
    protected $_productRepository, $_resultRedirect, $quote, $cartRep, $_responseFactory, $_url;

    public function __construct(Context $context, HttpRequest $request,
            ProductRepositoryInterface $productRepository,
            CheckoutCart $checkoutCart, ResponseFactory $responseFactory,
            UrlInterface $url) {

        parent::__construct($context);

        $this->_request = $request;
        $this->_productRepository = $productRepository;

        $this->_cart = $checkoutCart;

        $this->_responseFactory = $responseFactory;
        $this->_url = $url;
    }

    public function execute() {

        $data['qty'] = $this->_request->getParam('qty');
        $data['sku'] = $this->_request->getParam('sku');

        if (is_null($data['sku']) || is_null($data['qty'])) {
            $redirectUrl = $this->_url->getUrl('orderbysku/index/index');
            $this->_responseFactory->create()->setRedirect($redirectUrl)->sendResponse();
        }

        foreach ($data['sku'] as $key => $sku) {
            $product = $this->_productRepository->get($sku);
            $productId = $product->getId();
            $orderqty = $data['qty'][$key];
            
            if ($productId) {
                $params = array(
                    'product' => $productId,
                    'qty' => $orderqty,
                );

                try {
                    $this->_cart->addProduct($product, $params);
                    $this->_cart->save();
                    
                    $message = __($product->getName() . ' was successfully added to your shopping cart.');
                    $this->messageManager->addSuccess($message);
                    
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage("SKU: " . $product->getSku() ." - ". $e->getMessage());
                }
            }
        }

        $redirectUrl = $this->_url->getUrl('checkout/cart');
        $this->_responseFactory->create()->setRedirect($redirectUrl)->sendResponse();
        die;
    }

}
