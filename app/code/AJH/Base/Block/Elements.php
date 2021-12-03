<?php

namespace AJH\Base\Block;

use Magento\Framework\View\element\Template\Context;
use Magento\Customer\Model\Session;

class Elements extends \Magento\Framework\View\Element\Template {

    protected $_customerSession;

    public function __construct(Context $context, Session $session) {
        parent::__construct($context);

        $this->_customerSession = $session;
    }

    public function isCustomerLoggedIn() {
        return $this->_customerSession->isLoggedIn();
    }

}
