<?php

namespace AJH\D2R\Controller\Retailer;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\UrlInterface;
use Magento\Customer\Model\AddressFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Element\Template as FrameworkTemplate;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Customer\Model\Form as CustomerForm;

class Application extends \Magento\Framework\App\Action\Action {

    protected $_context, $_customerSession, $_urlInterface, $_customerAddress, $_frameworkTemplate, $_storeManager, $_logger, $_coreSession, $_customerFactory, $_scopeConfig, $_transportBuilder, $_customerForm;

    public function __construct(
    Context $context, CustomerSession $customerSession, UrlInterface $urlInterface, AddressFactory $customerAddress, FrameworkTemplate $frameworkTemplate, StoreManagerInterface $storeManager, LoggerInterface $logger, SessionManagerInterface $coreSession, CustomerFactory $customerFactory, ScopeConfigInterface $scopeConfig, TransportBuilder $transportBuilder, CustomerForm $customerForm) {

        $this->_customerSession = $customerSession;
        $this->_urlInterface = $urlInterface;
        $this->_customerAddress = $customerAddress;

        $this->_customerFactory = $customerFactory;

        $this->_frameworkTemplate = $frameworkTemplate;
        $this->_storeManager = $storeManager;

        $this->_logger = $logger;

        $this->_coreSession = $coreSession;
        $this->_scopeConfig = $scopeConfig;

        $this->_transportBuilder = $transportBuilder;

        $this->_customerForm = $customerForm;

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

    public function addressView() {
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

    public function postApplicationAction() {
        if (!$this->_validateFormKey() || !$this->getRequest()->isPost()
        ) {
            return $this->_redirect('*/*/');
        }

        $data = $this->getRequest()->getPost();

//        Mage::helper('d2r/retailer')->checkLogin($this->_urlInterface->getUrl('*/*/index'), $data);
        \AJH\D2R\Helper\Retailer::checkLogin($this->_urlInterface->getUrl('*/*/index'), $data);

        $response = [
            'errorMessage' => '',
            'errorMessages' => [],
            'messages' => [],
        ];

        $session = $this->_customerSession;
        $customer = $session->getCustomer();
        $storeId = $this->_storeManager->getStore()->getId();

        try {
            if ('exists' == $data['address_source']) {
                $address = $customer->getAddressById($data['address_id']);
                if (!$address->getId() || $address->getCustomerId() != $customer->getId()) {
                    throw new \Exception('Invalid address ID');
                }
            } else {
                $errors = array();
                $address = $this->_customerAddress;
                $addressForm = $this->_customerForm
                        ->setFormCode('customer_address_edit')
                        ->setEntity($address);
                $addressData = $addressForm->extractData($this->getRequest());
                $addressErrors = $addressForm->validateData($addressData);
                if ($addressErrors !== true) {
                    $errors = $addressErrors;
                }

                $addressForm->compactData($addressData);
                $address
                        ->setCustomerId($customer->getId())
                        ->setIsDefaultBilling(@$data['default_billing'])
                        ->setIsDefaultShipping(@$data['default_shipping']);

                $addressErrors = $address->validate();
                if ($addressErrors !== true) {
                    $errors = array_merge($errors, $addressErrors);
                }

                if (!count($errors)) {
                    $address->save();
                    $data['address_id'] = $address->getId();
                    $response['messages'][] = __('The address has been saved');
                } else {
                    $this->_getSession()->setAddressFormData($data);

                    $response['errorMessages'] = array();
                    foreach ($errors as $errorMessage) {
                        $response['errorMessages'][] = $errorMessage;
                    }
                }
            }

            // cleaning the data by removing address fields from there
            foreach (array(
        'firstname', 'lastname', 'company', 'telephone', 'fax', 'street', 'city',
        'region_id', 'region', 'postcode', 'country_id', 'default_billing', 'default_shipping',
        'form_key', 'i_confirm', 'address_source'
            ) as $fieldName) {
                unset($data[$fieldName]);
            }

            $contactEmail = (isset($data['email']) && $data['email']) ? $data['email'] : $customer->getEmail();

            $application = new Varien_Object($data);
            $application->setData('date_sent', AJH_D2R_Helper_Data::getDateFormatted(time(), 'MMMM d, YYYY'));

            $authKey = md5(rand(10000, 10000000));
            $application->setData('auth_key', $authKey);

//			$customerGroup = Mage::getModel('customer/group')->load($customer->getGroupId());

            $customer
                    ->setData(AJH_D2R_Helper_Data::ATTR_RETAILER_STATUS, AJH_D2R_Model_Source_Retailer_Status::CANDIDATE)
                    ->setData(AJH_D2R_Helper_Data::ATTR_APPLICATION_DATA, $application)
//				->setData(AJH_D2R_Helper_Data::ATTR_RETAILER_COST, $customerGroup->getData('cost_percent'))
                    ->save();

            // sending a notification to admin
            $adminEmailTemplate = $this->_transportBuilder
                    ->setDesignConfig(array(
                        'area' => 'adminhtml',
                        'store' => Mage_Core_Model_App::ADMIN_STORE_ID,
                    ))
//				->setReplyTo($customer->getEmail())
                    ->sendTransactional(
                    $this->_scopeConfig->getValue('d2r/email/admin_notify_template'), $this->_scopeConfig->getValue('d2r/email/sender_email_identity'), $this->_scopeConfig->getValue('d2r/email/admin_notify_email'), __('Retailer Application'), array(
                'application' => $application,
                'address' => $address,
                'customer' => $customer,
                'address_html' => $address->format('html'),
                'approve_url' => $this->_urlInterfacegetUrl('*/*/status', array(
                    'action' => \AJH\D2R\Model\Source\Retailer\Status::APPROVED,
                    'id' => $customer->getId(),
                    'key' => $authKey
                )),
                'decline_url' => $this->_urlInterface->getUrl('*/*/status', array(
                    'action' => \AJH\D2R\Model\Source\Retailer\Status::DECLINED,
                    'id' => $customer->getId(),
                    'key' => $authKey
                )),
                    ), $storeId
            );

            if (!$adminEmailTemplate->getSentSuccess()) {
                $this->_logger->log(sprintf('Error sending retailer application admin notification email, customer: %s', $this->_scopeConfig->getValue('d2r/email/admin_notify_email')
                ));
            }

            // sending a notification to customer
            $retailerEmailTemplate = $this->_transportBuilder
                    ->setDesignConfig(array(
                        'area' => 'frontend',
                        'store' => $storeId,
                    ))
                    ->setReplyTo($this->_scopeConfig->getValue('d2r/email/sender_email_identity'))
                    ->sendTransactional(
                    $this->_scopeConfig->getValue('d2r/email/customer_notify_template'), $this->_scopeConfig->getValue('d2r/email/sender_email_identity'), $contactEmail, $customer->getName(), array(
                'application' => $application,
                'address' => $address,
                'customer' => $customer,
                'address_html' => $address->format('html'),
                    ), $storeId
            );

            $retailerNotificationEmailSent = $retailerEmailTemplate->getSentSuccess();

            if (!$retailerNotificationEmailSent) {
                $this->_logger->log(sprintf('Error sending retailer application confirmation email to %s', $customer->getName() . ' <' . $contactEmail . '>'));
            }

            // responding to AJAX request
            $response['html'] = $this->getLayout()
                    ->createBlock('core/template')
                    ->setTemplate('vk_d2r/application/success.phtml')
                    ->setApplication($application)
                    ->setAddress($address)
                    ->setCustomer($customer)
                    ->setEmailSent($retailerNotificationEmailSent)
                    ->toHtml();
        } catch (\Exception $e) {
            $response['errorMessage'] = $e->getMessage();
        }

        $this->getResponse()->setBody(json_encode($response));
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

    public function statusAction() {
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

    public function testAction() {
        
    }

}
