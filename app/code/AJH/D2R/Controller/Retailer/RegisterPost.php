<?php

namespace AJH\D2R\Controller\Retailer;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Page\Config;
use AJH\D2R\Helper\Email;
use Magento\Customer\Model\Session;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\StoreManagerInterface;
use AJH\D2R\Helper\Distributor as DistributorHelper;
use AJH\D2R\Helper\Retailer as RetailerHelper;
use AJH\D2R\Model\Source\Retailer\Status as RetailerStatus;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Customer\Model\Form as CustomerForm;
use Magento\Customer\Model\Address as CustomerAddress;
use Magento\Customer\Model\Customer;
use AJH\D2R\Model\Retailer\Application;

use Magento\Customer\Api\Data\AddressInterfaceFactory;
use Magento\Customer\Api\AddressRepositoryInterface;

//use Ebizmarts\MageMonkey\Helper\Data as MonkeyHelperData;

class RegisterPost extends \Magento\Framework\App\Action\Action {

    protected $_pageConfig, $_customerSession, $_helper, $_retailerHelper, $_distributorHelper, $_emailHelper;
    protected $_retailerStatus, $_request, $_customer, $_customerForm, $_customerAddress, $_coreEmail, $_customerCollection;
    protected $_storeManager, $_monkey, $_customerFactory;
    protected $_transportBuilder, $_inlineTranslation;

    public function __construct(Context $context, Config $pageConfig,
            RequestInterface $request, Email $email, Session $customerSession,
            StoreManagerInterface $storeManager,
            DistributorHelper $distributorHelper,
            RetailerHelper $retailerHelper, RetailerStatus $retailerStatus,
            CustomerFactory $customerFactory,
            TransportBuilder $transportBuilder,
            StateInterface $inlineTranslation, CustomerForm $customerForm,
            CustomerAddress $customerAddress, Customer $customer,
            Application $retailerApplication, AddressRepositoryInterface $addressRepository,
            AddressInterfaceFactory $dataAddressFactory) {

        parent::__construct($context);

        $this->_pageConfig = $pageConfig;
        $this->_request = $request;

        $this->_emailHelper = $email;
        $this->_customerSession = $customerSession;

        $this->_customer = $customer;
        $this->_customerForm = $customerForm;
        $this->_customerAddress = $customerAddress;

        $this->_retailerApplication = $retailerApplication;

        $this->_customerCollection = $customerFactory;
        $this->_storeManager = $storeManager;

        $this->_distributorHelper = $distributorHelper;
        $this->_retailerHelper = $retailerHelper;

        $this->_retailerStatus = $retailerStatus;

        $this->_transportBuilder = $transportBuilder;
        $this->_inlineTranslation = $inlineTranslation;
        
        $this->_dataAddressFactory = $dataAddressFactory;
        $this->_addressRepository = $addressRepository;

//        $this->_monkey = $monkey;
    }

    public function execute() {
        $res = [
            'messages' => [],
        ];

        try {
            $customer = $this->_saveCustomer($this->_request, $res);
//            $address = $this->_saveAddress($this->_request, $res, $customer);
            $address = $this->saveAddress($this->_request, $customer);

            $emailHelper = $this->_emailHelper;
            $emailHelper->notifySales($customer, $address);
            $emailHelper->notifyDistributor($customer, $address);
            $emailHelper->notifyCustomer($customer, $address, $emailHelper->CUSTOMER_NOTIFICATION_TYPE_RETAILER_APP_SENT);

            $res['html'] = $this->_view->getLayout()->createBlock('AJH\D2R\Block\Retailer\Register\Success')->toHtml();
        } catch (\Exception $e) {
            $res['errorMessage'] = $e->getMessage();
        }

        echo json_encode($res);
        die;
    }

    private function distributor($distributor_id) {
        $distributor = array();

        $distributor[169] = "Bonnett Enterprises, Tarentum,PA";
        $distributor[157] = "Capital Tire Inc., Ferndale,MI";
        $distributor[170] = "Covenant Distributing, Fresno,CA";
        $distributor[158] = "CRW Parts Inc, Baltimore,MD";
        $distributor[160] = "CRW Parts Inc, Capitol Heights,MD";
        $distributor[161] = "CRW Parts Inc, Hagerstown,MD";
        $distributor[162] = "CRW Parts Inc, Jessup,MD";
        $distributor[163] = "CRW Parts Inc, Norfolk,VA";
        $distributor[164] = "CRW Parts Inc, Richmond,VA";
        $distributor[165] = "CRW Parts Inc, Roanoke,VA";
        $distributor[166] = "CRW Parts Inc, Salisbury,MD";
        $distributor[167] = "CRW Parts Inc, Wilmington,DE";
        $distributor[159] = "CRW Parts Inc / Fleet Parts, Baltimore,MD";
        $distributor[142] = "NTD - National Tire Distributors, Kelowna,BC";
        $distributor[141] = "NTD - National Tire Distributors, Calgary,AB";
        $distributor[145] = "NTD - National Tire Distributors, Edmonton,AB";
        $distributor[146] = "NTD - National Tire Distributors, Langley,BC";
        $distributor[147] = "NTD - National Tire Distributors, Burnaby,BC";
        $distributor[148] = "NTD - National Tire Distributors, Prince George,BC";
        $distributor[149] = "NTD - National Tire Distributors, Victoria,BC";
        $distributor[150] = "NTD - National Tire Distributors, Regina,SK";
        $distributor[151] = "NTD - National Tire Distributors, Saskatoon,SK";
        $distributor[152] = "NTD - National Tire Distributors, Winnipeg,MB";
        $distributor[207] = "PDQ TPMS Distributor, Oklahoma City,OK";
        $distributor[213] = "PDQ TPMS Distributor - AL, Montgomery,AL";
        $distributor[209] = "PDQ TPMS Distributor - AR, Little Rock,AR";
        $distributor[205] = "PDQ TPMS Distributor - AZ, Phoenix,AZ";
        $distributor[216] = "PDQ TPMS Distributor - CT, Hartford,CT";
        $distributor[219] = "PDQ TPMS Distributor - GA, Atlanta,GA";
        $distributor[200] = "PDQ TPMS Distributor - ID, Boise,ID";
        $distributor[206] = "PDQ TPMS Distributor - KY, Frankfort,KY";
        $distributor[212] = "PDQ TPMS Distributor - LA, Baton Rouge,LA";
        $distributor[218] = "PDQ TPMS Distributor - MA, Boston,MA";
        $distributor[210] = "PDQ TPMS Distributor - ME, Augusta,ME";
        $distributor[211] = "PDQ TPMS Distributor - MS, Jackson,MS";
        $distributor[193] = "PDQ TPMS Distributor - MT, Helena,MT";
        $distributor[215] = "PDQ TPMS Distributor - NC, Raleigh,NC";
        $distributor[198] = "PDQ TPMS Distributor - ND, Bismarck,ND";
        $distributor[220] = "PDQ TPMS Distributor - NH, Concord,NH";
        $distributor[214] = "PDQ TPMS Distributor - NJ, Trenton,NJ";
        $distributor[202] = "PDQ TPMS Distributor - NM, Santa Fe,NM";
        $distributor[203] = "PDQ TPMS Distributor - NV, Carson City,NV";
        $distributor[222] = "PDQ TPMS Distributor - OH, Columbus,OH";
        $distributor[199] = "PDQ TPMS Distributor - OR, Salem,OR";
        $distributor[225] = "PDQ TPMS Distributor - RI, Providence,RI";
        $distributor[217] = "PDQ TPMS Distributor - SC, Columbia,SC";
        $distributor[208] = "PDQ TPMS Distributor - TN, Nashville,TN";
        $distributor[204] = "PDQ TPMS Distributor - UT, Salt Lake City,UT";
        $distributor[221] = "PDQ TPMS Distributor - VT, Montpelier,VT";
        $distributor[195] = "PDQ TPMS Distributor - WA, Olympia,WA";
        $distributor[223] = "PDQ TPMS Distributor - WV, Charleston,WV";
        $distributor[224] = "PDQ TPMS Distributor - WY, Cheyenne,WY";
        $distributor[153] = "Revolution Supply, Garden Grove,CA";
        $distributor[154] = "Revolution Supply Co Inc, Garden Grove,CA";
        $distributor[168] = "United Auto Supply, Syracuse,NY";
        $distributor[156] = "US Auto Force - Alsip, ALSIP,IL";
        $distributor[176] = "US Auto Force - Bridgeton, Bridgeton,MO";
        $distributor[186] = "US Auto Force - Colorado Springs, Colorado Springs,CO";
        $distributor[171] = "US Auto Force - Combined Locks, Kimberly,WI";
        $distributor[185] = "US Auto Force - Denver, Aurora,CO";
        $distributor[184] = "US Auto Force - Des Moines, Des Moines,IA";
        $distributor[192] = "US Auto Force - Fort Lauderdale, ,FL";
        $distributor[187] = "US Auto Force - Gering, Gering,NE";
        $distributor[172] = "US Auto Force - Glendale Heights, Glendale Heights,IL";
        $distributor[155] = "US Auto Force - Golden, Golden,MO";
        $distributor[197] = "US Auto Force - Houston, Houston,TX";
        $distributor[188] = "US Auto Force - Kansas City, Kansas City,KS";
        $distributor[179] = "US Auto Force - Madison, Madison,WI";
        $distributor[174] = "US Auto Force - Mendota Heights, Mendota Heights,MN";
        $distributor[190] = "US Auto Force - Miami, Miami,FL";
        $distributor[173] = "US Auto Force - Niles, Niles,IL";
        $distributor[177] = "US Auto Force - Omaha, Omaha,NE";
        $distributor[191] = "US Auto Force - Orlando, Orlando,FL";
        $distributor[181] = "US Auto Force - Plymouth, ,MN";
        $distributor[182] = "US Auto Force - Roselle, Roselle,IL";
        $distributor[175] = "US Auto Force - Roseville, Roseville,MN";
        $distributor[196] = "US Auto Force - San Antonio, San Antonio,IL";
        $distributor[178] = "US Auto Force - Sioux Falls, Sioux Falls,SD";
        $distributor[183] = "US Auto Force - Skokie, Skokie,IL";
        $distributor[194] = "US Auto Force - Tampa, Tampa,FL";
        $distributor[189] = "US Auto Force - Valaparaiso, Valparaiso,IN";
        $distributor[180] = "US Auto Force - West Allis, West Allis,WI";

        return $distributor[$distributor_id];
    }

    private function sendMail($customer) {
        $distributor_id = $customer->getData('distributor_id');

        //if got a "getSource" error, it means the attributes does not exists
        $distributor = $customer->getResource()
                        ->getAttribute('distributor_id')
                        ->getSource()->getOptionText($distributor_id);

        $name = $customer->getFirstname() . ' ' . $customer->getLastname();
        $html = "<p><strong>Dear {$customer->getFirstname()}</strong>,</p>
<br/>
<p>Thank you for registering on our site. You are on your way to experience the full potential of PDQ website https://pdqtpms.com/. We will be sending an approval email shortly. Once approved you will you have access to:</p>
<br/>
<ul>
<li>Re-learn procedures . link https://pdqtpms.com/relearn</li>
<li>Reverse lookup (search by part number) <p><img src=\"" . $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB) . "images/search-by-partnumber.png\" alt=\"\" /></p></li>
<li>TPMS help tickets link https://pdqtpms.com/tpms-help-form</li>
<li>Members only discount and promotions</li>
</ul>
<p>Here is your mail in rebate coupon for your first purchase of 24 PDQ Dual Band programmable sensors https://pdqtpms.com/pdq-001-24. Additionally, to get you started with PDQ-TPMS products we would like to offer you a promotional price of $599.99 for 24 sensors ($24.96 per sensor). After the mail in rebate, your net cost is a low $23.96 each. This price includes free two day shipping. This coupon is only valid when used when purchasing from <strong>{$distributor}</strong>.</p>"
                . "<p><img src=\"" . $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB) . "images/PDQ_Mail-in-rebate.jpg\" alt=\"\" /></p>";

        $html .= "<br/><br/><br/><br/>";
        $html .= "<p>Thank You</p>";

        $to = [$customer->getEmail()];
        $email = new \Zend_Mail();
        $email->setSubject("$24 Mail In Rebate");
        $email->setBodyHtml($html);
        $email->setFrom("Sales@pdqtpms.com", "PDQTPMS Sales");
        $email->addTo($to, $name);
        $email->send();
    }

    protected function _saveAddress($request, &$res, $customer) {
        $customerId = $customer->getId();

        $address = $this->_customerAddress;

        $addressId = (int) $this->_request->getParam('address_id');
        if ($addressId) {
            $address->load($addressId);
            if (!$address->getId() || $address->getCustomerId() != $customerId) {
//                Mage::throwException('Invalid address ID');
                throw new \Exception('Invalid address ID');
            }
        }

        die('---' . $addressId);

        $addressForm = $this->_customerForm
                ->setFormCode('customer_address_edit')
                ->setEntity($address);
        $addressData = $addressForm->extractData($request);

        var_dump($addressData);
        die('|address|');

        $addressErrors = $addressForm->validateData($addressData);
        $this->_checkErrors($addressErrors, $res, 'Form error: check address fields');
        $addressForm->compactData($addressData);

        if ($addressId) {
            $defaultBilling = $customer->getDefaultBillingAddress();
            if ($defaultBilling) {
                $isDefaultBilling = ($defaultBilling->getId() == $addressId);
            } else {
                $isDefaultBilling = true;
            }

            $defaultShipping = $customer->getDefaultShippingAddress();
            if ($defaultShipping) {
                $isDefaultShipping = ($defaultShipping->getId() == $addressId);
            } else {
                $isDefaultShipping = true;
            }
        } else {
            $isDefaultBilling = true;
            $isDefaultShipping = true;
        }

        $address
                ->setCustomerId($customerId)
                ->setIsDefaultBilling($isDefaultBilling)
                ->setIsDefaultShipping($isDefaultShipping);

        $addressErrors = $address->validate();
        $this->_checkErrors($addressErrors, $res, 'Form error: check address fields');

        $address->save();

        return $address;
    }

    public function saveAddress($request, $customer) {
        $data = $request->getParams();
        $newAddressId = 0;
        
        /* save address as customer */
        $address = $this->_dataAddressFactory->create();
        $address->setFirstname($data['firstname']);
        $address->setLastname($data['lastname']);
        $address->setTelephone($data['telephone']);        

        $street[] = $data['street']; //pass street as array        
        $address->setStreet($street);                

        $address->setCity($data['city']);
        $address->setCountryId($data['country_id']);
        $address->setPostcode($data['postcode']);
        $address->setRegionId($data['region_id']);
        $address->setIsDefaultShipping(1);
        $address->setIsDefaultBilling(1);
        $address->setCustomerId($customer->getId());
        try {
            $this->_addressRepository->save($address);
        } catch (\Exception $e) {
            return __('Error in shipping/billing address.');
        }
        
        foreach($customer->getAddresses() as $_address){
            $newAddressId = $_address->getId();
        }               
        
        return $this->_addressRepository->getById($newAddressId);
    }

    protected function _saveCustomer($request, &$res) {
        $customerSession = $this->_customerSession;

        $customerId = (int) $this->_request->getParam('customer_id');
        if ($customerId) {
            if (!$customerSession->isLoggedIn() || $customerSession->getCustomerId() != $customerId
            ) {
                new \Exception('Invalid customer ID');
            }
            $customer = $customerSession->getCustomer();
        } else {
            $email = $this->_request->getParam('email');
            if ($email) {
                $testCustomer = $this->_customer
                        ->setWebsiteId($this->_storeManager->getStore()->getWebsite()->getId())
                        ->loadByEmail($email);
                if ($testCustomer->getId()) {
                    new \Exception('A customer with same email already exists');
                }
            }
            $customerId = null;
            $customer = $this->_customer
                    ->setWebsiteId($this->_storeManager->getStore()->getWebsite()->getId());
        }

        $customerForm = $this->_customerForm;
        if ($customerId) {
            $customerForm->setFormCode('customer_account_edit');
        } else {
            $customerForm->setFormCode('customer_account_create');
        }
        $customerForm->setEntity($customer);
        $customerData = $customerForm->extractData($request);        

        $customerErrors = $customerForm->validateData($customerData);
        $this->_checkErrors($customerErrors, $res, 'Form error: check customer fields');

        $customerForm->compactData($customerData);

        if (!$customerId) {
            $customer->setPassword($request->getParam('password'));
            $customer->setPasswordConfirmation($request->getParam('confirmation'));
        } else {
            // If email change was requested then set flag
            $isChangeEmail = ($customer->getOldEmail() != $customer->getEmail()) ? true : false;
            $customer->setIsChangeEmail($isChangeEmail);

            $customer->cleanPasswordsValidationData();

            // Reset all password reset tokens if all data was sufficient and correct on email change
            if ($customer->getIsChangeEmail()) {
                $customer->setRpToken(null);
                $customer->setRpTokenCreatedAt(null);
            }
        }

        $customerErrors = $customer->validate();
        $this->_checkErrors($customerErrors, $res, 'Form error: check customer fields');

        if ($customerId) {
            $customer->cleanPasswordsValidationData();
            if ($customer->getIsChangeEmail()) {
                $customer->setRpToken(null);
                $customer->setRpTokenCreatedAt(null);
            }
        }

//        $customer->setResettooluse('PDQ');

        $customer
                ->setData($this->_distributorHelper::ATTR_DISTRIBUTOR_ID, $this->_request->getParam('distributor_id'))
                ->setData($this->_retailerHelper::ATTR_RETAILER_STATUS, $this->_retailerStatus::CANDIDATE)
                ->setData($this->_retailerHelper::ATTR_RETAILER_RESET_TOOL_USE, $this->_request->getParam('reset_tool_use'))
                ->setData($this->_retailerHelper::ATTR_RETAILER_PDQ_SERIAL, $this->_request->getParam('pdqserialnumber'))
                ->setData($this->_retailerHelper::ATTR_RETAILER_OTHER_BRAND, $this->_request->getParam('otherbrand'))
                ->setData($this->_retailerHelper::ATTR_APPLICATION_DATA, $this->_retailerApplication->getData_());

        $customer->save();


        if ($customerId) {
            if ($customer->getIsChangeEmail() || $customer->getIsChangePassword()) {
                $customer->sendChangedPasswordOrEmail();
            }
        }

//        $this->_monkey->subscribeToList($customer, 0);
//        $this->sendMail($customer);

        if (!$customerId) {
//            Mage::dispatchEvent('customer_register_success', ['account_controller' => $this, 'customer' => $customer]);
//            $nameobj = new \Magento\Framework\DataObject(array('name' => 'Magecomp'));
            $this->_eventManager->dispatch('customer_register_success', ['account_controller' => $this, 'customer' => $customer]);

            $res['messages'] = array_merge($res['messages'], $this->_successProcessRegistration($customer));
        }



        return $customer;
    }

    protected function _checkErrors($errors, &$res, $message = 'Form error') {
        if (is_array($errors)) {
            if (!isset($res['errorMessages'])) {
                $res['errorMessages'] = [];
            }

            foreach ($errors as $error) {
                $res['errorMessages'][] = $error;
            }

            throw new \Exception($message);
        }
    }

    /**
     * Success Registration
     *
     * @param Mage_Customer_Model_Customer $customer
     * @return Mage_Customer_AccountController
     */
    protected function _successProcessRegistration(\Magento\Customer\Model\Customer $customer) {
        $res = [];

        $session = $this->_customerSession;
        if ($customer->isConfirmationRequired()) {
            $customer->sendNewAccountEmail('confirmation', $session->getBeforeAuthUrl(), $this->_storeManager->getStore()->getId(), $this->_request->getParam('password')
            );
            $res[] = 'Account confirmation is required. Please check your email for the confirmation link';
        } else {
            $session->setCustomerAsLoggedIn($customer);
        }
        return $res;
    }

}
