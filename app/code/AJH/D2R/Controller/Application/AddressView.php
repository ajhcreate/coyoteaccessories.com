<?php

namespace AJH\D2R\Controller\Application;

use Magento\Customer\Model\AddressFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Element\Template as FrameworkTemplate;

class AddressView extends \Magento\Framework\App\Action\Action {

    protected $_context, $_customerSession, $_urlInterface, $_customerAddress, $_frameworkTemplate, $_storeManager, $_logger, $_coreSession, $_customerFactory, $_scopeConfig, $_transportBuilder, $_customerForm;

    public function __construct(Context $context, AddressFactory $customerAddress, FrameworkTemplate $frameworkTemplate) {

        
        $this->_customerAddress = $customerAddress;        

        $this->_frameworkTemplate = $frameworkTemplate;        
        
        parent::__construct($context);
    }

    public function execute() {
        $addressId = $this->getRequest()->getParam('id');

        $result = array(
            'errorMessage' => '',
        );

        try {
            $address = $this->_customerAddress->load($addressId);

            $customer = $address->getCustomer();
            if ($customer) {
                $address->setEmail($customer->getEmail());
            }

            $result['html'] = $this->getLayout()
                    ->createBlock($this->_frameworkTemplate)
                    ->setTemplate('AJH_D2R::retailer/application/address/view.phtml')
                    ->setAddress($address)
                    ->toHtml();
        } catch (\Exception $e) {
            $result['errorMessage'] = $e->getMessage();
        }

        $this->getResponse()->setBody(json_encode($result));
    }

}
