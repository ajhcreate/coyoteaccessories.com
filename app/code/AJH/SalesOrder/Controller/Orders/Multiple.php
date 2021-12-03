<?php

namespace AJH\SalesOrder\Controller\Orders;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Catalog\Model\ProductRepository;
use Magento\Checkout\Model\Cart;

class Multiple extends \Magento\Framework\App\Action\Action {

    protected $_pageFactory, $_helper, $_urlInterface, $_transportBuilder, $http;
    protected $_scopeConfig, $_customerSession;
    protected $cart;
    protected $_messageManager;

    public function __construct(
    \Magento\Framework\App\Action\Context $context,
            \Magento\Framework\View\Result\PageFactory $pageFactory,
            \Magento\Framework\UrlInterface $urlInterface,
            \Magento\Framework\App\ResponseFactory $responseFactory,
            \Magento\Framework\Message\ManagerInterface $messageManager,
            CustomerSession $customerSession,
            ProductRepository $productRepository, Cart $cart) {

        $this->_pageFactory = $pageFactory;
        $this->_url = $urlInterface;
        $this->_customerSession = $customerSession;
        $this->cart = $cart;
        $this->_productRepository = $productRepository;
        $this->_responseFactory = $responseFactory;

        $this->_messageManager = $messageManager;

        return parent::__construct($context);
    }

    public function execute() {
        $items = $this->getRequest()->getParam('skus');
        $quantity = $this->getRequest()->getParam('qty');
        $form_key = $this->getRequest()->getParam('form_key');

//        var_dump($items);
        //If any of the product we are adding, is not valid, we are not adding it to cart.
        if (count($items)) {
//            try {
            $msg = '';
            $err_msg = '';
            foreach ($items as $key => $item) {
                $product = $this->_productRepository->get($item);

                if ($product->isSaleable()) {
                    $params = array(
                        'form_key' => $form_key,
                        'product' => $product->getId(),
                        'qty' => $quantity[$key]
                    );

                    $this->cart->addProduct($product, $params);

                    $msg .= $product->getName() . " is successfully added into cart. <br/>";
                }else{
                    $err_msg .= $product->getName() . " is not available. <br/>";
                }
            }
            
            $this->cart->save();

            if($msg!==''){
                $this->_messageManager->addSuccess('core/session')->addSuccess(__($msg));
            }
            
            if($err_msg!==''){
                $this->_messageManager->addError('core/session')->addError(__($err_msg));
            }
            
            $RedirectUrl = $this->_url->getUrl('checkout/cart');
            $this->_responseFactory->create()->setRedirect($RedirectUrl)->sendResponse();

            exit;
//            } catch (Exception $e) {
////                Mage::getSingleton('core/session')->addError(Mage::helper('checkout')->__($e->getMessage()));
//                $this->_redirect('checkout/cart');
//            }
        }
    }

}
