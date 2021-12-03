<?php

namespace AJH\D2R\Controller\Application;

use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\CustomerFactory;

class Status extends \Magento\Framework\App\Action\Action {

    protected $_context, $_customerFactory;

    public function __construct(Context $context, CustomerFactory $customerFactory) {

        $this->_customerFactory = $customerFactory;

        parent::__construct($context);
    }

    public function execute() {
        $status = $this->getRequest()->getParam('action');

        switch ($status) {
            case \AJH\D2R\Model\Source\Retailer\Status::APPROVED:
                $statusName = 'approved';
                $result = $this->_changeRetailerStatus($status, $statusName);
                break;

            case \AJH\D2R\Model\Source\Retailer\Status::DECLINED:
                $statusName = 'declined';
                $result = $this->_changeRetailerStatus($status, $statusName);
                break;

            default:
                $statusName = 'not set: status unknown';
                $result = false;
        }

//        $this
//                ->loadLayout()
//                ->_initLayoutMessages('customer/session')
//                ->_initLayoutMessages('catalog/session');

        $this->getLayout()->getBlock('d2r_retailer_application_status')
                ->setResult($result)
                ->setRetailerStatus($status)
                ->setRetailerStatusName($statusName)
                ->setCustomer($this->_customerFactory->load($this->getRequest()->getParam('id')));

        $this->renderLayout();
    }

    protected function _changeRetailerStatus($status, $statusName) {
        $customerId = $this->getRequest()->getParam('id');
        $result = false;

        try {
            if ($customerId) {
                $customer = $this->_customerFactory->load($customerId);
            }

            if (!$customerId || $customer->getId() !== $customerId) {
                throw new \Exception('No such customer');
            }

            $application = $customer->getData(\AJH\D2R\Helper\Retailer::ATTR_APPLICATION_DATA);
            $hash = $application->getData('auth_key');

            if (!$hash || $hash !== $this->getRequest()->getParam('key')) {
                throw new \Exception('Wrong security key');
            }

            $retailerStatus = $customer->getData(\AJH\D2R\Helper\Retailer::ATTR_RETAILER_STATUS);

            if (!in_array($retailerStatus, array(
                        \AJH\D2R\Model\Source\Retailer\Status::CANDIDATE,
//				AJH_D2R_Model_Source_Retailer_Status::APPROVED,
//				AJH_D2R_Model_Source_Retailer_Status::DECLINED,
//				AJH_D2R_Model_Source_Retailer_Status::TERMINATED
                    ))) {
                throw new \Exception('Wrong retailer status');
            }

//			$application->unsData('auth_key'); // unsetting the key, thus making current action disposable
//			$customer->setData(AJH_D2R_Helper_Data::ATTR_APPLICATION_DATA, $application)

            $customer
                    ->setData(\AJH\D2R\Helper\Retailer::ATTR_RETAILER_STATUS, $status)
                    ->save();

            $this->_coreSession->addSuccess(sprintf(
                            'Retailer application from customer %s <%s> has been %s', $customer->getName(), $customer->getEmail(), $statusName
            ));

            $result = true;
        } catch (\Exception $e) {
            $this->_logger->log('Error assigning retailer status to customer: Message=%s, Customer ID=%s, Status=%s, Remote IP=%s', $e->getMessage(), $customerId, $statusName, $_SERVER['REMOTE_ADDR']
            );
            $this->_coreSession->addError('Action error');
        }

        return $result;
    }

}
