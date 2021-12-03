<?php

namespace AJH\D2R\Controller\Retailer;

use Magento\Framework\App\Action\Context;
use Magento\Framework\UrlInterface;
use Magento\Customer\Model\Session as CustomerSession;

class Index extends \Magento\Framework\App\Action\Action {

    protected $_context, $_customerSession, $_urlInterface;

    public function __construct(Context $context, CustomerSession $customerSession, UrlInterface $urlInterface) {

        $this->_customerSession = $customerSession;
        $this->_urlInterface = $urlInterface;

        parent::__construct($context);
    }

    public function execute() {
        $session = $this->_customerSession;

        if ($session->isLoggedIn()) {
            $session->unsBeforeAuthUrl();
        } else {
            $session->setBeforeAuthUrl($this->_urlInterface->getUrl('*/*/*', array('_current' => true)));
        }

        if (\AJH\D2R\Helper\Data::isActiveRetailer()) {
            $this->getResponse()->setRedirect($this->_urlInterface->getUrl('d2r'));
            return;
        }

        $this->loadLayout()->renderLayout();
//                ->_initLayoutMessages('customer/session')
//                ->_initLayoutMessages('catalog/session')
    }

}
