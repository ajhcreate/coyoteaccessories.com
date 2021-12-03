<?php

namespace AJH\D2R\Controller\Retailer;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\App\RequestInterface;
use AJH\D2R\Helper\Data as D2RHelperData;
use AJH\D2R\Helper\Retailer;
use AJH\D2R\Helper\Distributor;
use AJH\D2R\Model\Source\Retailer\Status;


class DistributorSearch extends Template {

    protected $_pageConfig, $_customerSession, $_helper, $_retailerHelper, $_distributorHelper;
    protected $_retailerStatus, $_request;

    public function __construct(Context $context, \Magento\Framework\View\Page\Config $pageConfig, \Magento\Customer\Model\Session $customerSession, D2RHelperData $helper, Retailer $retailer, Distributor $distributor, Status $status, RequestInterface $request
    ) {
        parent::__construct($context);
        $this->_helper = $helper;
        $this->_retailerHelper = $retailer;
        $this->_distributorHelper = $distributor;
        
        $this->_pageConfig = $pageConfig;
        $this->_customerSession = $customerSession;
        
        $this->_retailerStatus = $status;
        
        $this->_request = $request;
    }

    public function execute() {
        $res = [];
        $session = $this->_customerSession;

        try {
            if (!$session->isLoggedIn()) {
                throw new Exception('Your customer session has expired. Please relogin.');
            }
            $customer = $session->getCustomer();
            if ($customer->getData($this->_retailerHelper->ATTR_RETAILER_STATUS) != $this->_retailerStatus->NONE) {
                throw new Exception('You cannot select a distributor now.');
            }

            $id = $this->_request->getParam('id');
            if (!$id) {
                throw new Exception('No distributor specified!');
            }

            $customer
                    ->setData($this->_distributorHelper->ATTR_DISTRIBUTOR_ID, $id)
                    ->save();
        } catch (Exception $e) {
//            Mage::logException($e);
            $res['errorMessage'] = $e->getMessage();
        }

        echo json_encode($res);
        die;
    }

}
