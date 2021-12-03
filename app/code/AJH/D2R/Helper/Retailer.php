<?php

namespace AJH\D2R\Helper;

use AJH\D2R\Helper\Data as D2RHelperData;
use Magento\Framework\App\State;
use Magento\Store\Model\StoreManagerInterface;
use AJH\D2R\Model\Source\Retailer\Status as RetailerStatus;
use Magento\Customer\Model\CustomerFactory;
use Magento\Directory\Model\Country;
use Magento\Customer\Model\SessionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;

use Magento\Backend\Model\Session\Quote as SessionQuote;

class Retailer extends D2RHelperData {

    const ATTR_APPLICATION_DATA = 'application';
    const ATTR_RETAILER_LATLNG = 'retailer_latlng';
    const ATTR_RETAILER_STATUS = 'retailer_status';
    const ATTR_RETAILER_COST = 'retailer_cost';
    const ATTR_RETAILER_RESET_TOOL_USE = 'reset_tool_use';
    const ATTR_RETAILER_PDQ_SERIAL = 'pdq_serial_number';
    const ATTR_RETAILER_OTHER_BRAND = 'other_brand';
    const ATTR_CUSTOMER_JOBPOSITION = 'position';

    protected static $isEnabled = null;
    protected $_isActiveRetailer = null;
    protected static $_customerGroupCostPercent = null;
    protected static $_customerCostPercent = null;
    protected static $_customer = null;
    protected $_state, $_storeManager, $_retailerStatus, $_customerCollection, $_country, $_customerSession;
    protected $_scopeConfig;
    protected $_sessionQuote;

    public function __construct(State $state, StoreManagerInterface $storeManager, RetailerStatus $retailerStatus, CustomerFactory $customerCollection, Country $country, SessionFactory $customerSession, ScopeConfigInterface $scopeConfig, SessionQuote $sessionQuote) {
        $this->_state = $state;
        $this->_retailerStatus = $retailerStatus;
        $this->_storeManager = $storeManager;
        $this->_customerCollection = $customerCollection;

        $this->_country = $country;
        $this->_customerSession = $customerSession->create();
        $this->_scopeConfig = $scopeConfig;
        
        $this->_sessionQuote = $sessionQuote;
    }

    public function isEnabled() {
        if (null === $this->isEnabled) {
            $this->isEnabled = (bool) $this->_scopeConfig->getValue('d2r/general/enabled');
        }
        return $this->isEnabled;
    }

    public function getCustomerSession() {
        if (Mage::app()->getStore()->isAdmin()) {
            return $this->_sessionQuote;
        } else {
            return $this->_customerSession;
        }
    }

    public function getCustomer() {
        if (null === $this->_customer) {
//            if (Mage::app()->getStore()->isAdmin()) {
//                $this->_customer = $this->_sessionQuote->getCustomer();
//            } else {
//                $this->_customer = $this->_customerSession->getCustomer();
//            }
        }
        return $this->_customer;
    }

    public function getCustomerGroupCostPercent() {
        if (null === $this->_customerGroupCostPercent) {
            $groupId = self::getCustomer()->getGroupId();
            $this->_customerGroupCostPercent = (Mage_Customer_Model_Group::NOT_LOGGED_IN_ID == $groupId) ? 0 : floatval(Mage::getModel('customer/group')->load($groupId)->getCostPercent());
        }
        return $this->_customerGroupCostPercent;
    }

    public function getCustomerRetailerCostPercent() {
        if (null === $this->_customerCostPercent) {
            $this->_customerCostPercent = floatval(self::getCustomer()->getData(self::ATTR_RETAILER_COST));
            if (!$this->_customerCostPercent) {
                $this->_customerCostPercent = self::getCustomerGroupCostPercent();
            }
        }
        return $this->_customerCostPercent;
    }

    public function checkLogin($url = '', $data = null) {
        $session = $this->_customerSession;
        if (!$session->isLoggedIn()) {
            $session->setBeforeAuthUrl($url ? $url : $this->getUrl('*/*/*', array('_current' => true)));
            if (null !== $data) {
                if ($session->getFormData()) {
                    $session->unsFormData();
                }
                $session->setFormData($data);
            }
            $this->getResponse()->setRedirect($this->_storeManager->getStore()->getUrl('d2r/retailer/login'));
            return false;
        }
        return true;
    }

    public function isLoginRequired() {
        $session = $this->_customerSession;
        return !$session->isLoggedIn();
    }

    public function isApplicationSent() {
        $retailerStatus = $this->_customerSession->getCustomer()->getData(self::ATTR_RETAILER_STATUS);
        return (null !== $retailerStatus) && (\AJH\D2R\Model\Source\Retailer\Status::NONE != (int) $retailerStatus);
    }

    public function isActiveRetailer() {
//        echo $this->_customerSession->getCustomer()->getData(self::ATTR_RETAILER_STATUS);
//        die('is logged in');
        if ($this->_customerSession->isLoggedIn() && null === $this->_isActiveRetailer) {
            $this->_isActiveRetailer = (\AJH\D2R\Model\Source\Retailer\Status::APPROVED === (int) $this->_customerSession->getCustomer()->getData(self::ATTR_RETAILER_STATUS));
        }
//        return $this->_isActiveRetailer;
        return $this->_customerSession->isLoggedIn();
    }

    public function isOrotekActiveRetailer() {
        if (null === $this->_isActiveRetailer) {
            $this->_isActiveRetailer = (\AJH\D2R\Model\Source\Retailer\Status::ORAPPROVED === (int) $this->_customerSession->getCustomer()->getData(self::ATTR_RETAILER_STATUS));
        }
        return $this->_isActiveRetailer;
    }

    public function requireLogin($url = '') {
        $session = $this->_customerSession;
        if (!$session->isLoggedIn()) {
            $session->setBeforeAuthUrl($url ? $url : $this->getUrl('*/*/*', array('_current' => true)));
            $this->getResponse()->setRedirect($this->_storeManager->getStore()->getUrl('d2r/retailer/login'));
            return false;
        }
        return true;
    }

    public function getRetailerApplicationFields() {
        return array(
            'address' => ''
        );
    }

    public function getRetailerApplicationAddressFields() {
        return array(
            'prefix' => '',
            'firstname' => '',
            'middlename' => '',
            'lastname' => '',
            'suffix' => '',
            'company' => '',
            'street' => '',
            'city' => '',
            'country_id' => '',
            'region_id' => '',
            'postcode' => '',
            'telephone' => '',
            'fax' => '',
            'vat_id' => '',
        );
    }

    public function calculateCostPlusPrice($cost, $price = 0, $msrp = 0) {
        $normalizedCost = floatval($cost);
        $gainPercent = $this->getCustomerRetailerCostPercent();
        if ($normalizedCost && $gainPercent) {
            return $normalizedCost * (100 + $gainPercent) / 100;
        }
        $normalizedPrice = floatval($price);
        $res = $normalizedPrice ? $normalizedPrice : floatval($msrp);
        if ($res) {
            return $res;
        }
        $defaultGainPercent = floatval(trim($this->_scopeConfig->getValue('d2r_retailer/pricing/default_cost_plus'), ' %'));
        if ($normalizedCost && $defaultGainPercent) {
            return $normalizedCost * (100 + $defaultGainPercent) / 100;
        }
        return false;
    }

    public function getRetailerHomeUrl() {
        return $this->getUrl('d2r/retailer/home', array('_secure' => true));
    }

    /**
     * Get store identifier
     *
     * @return  int
     */
    public function getStoreId() {
        return $this->_storeManager->getStore()->getId();
    }

    public function getRetailers() {
        $res = [];
        $websiteId = $this->getStoreId();
        $status = $this->_retailerStatus::APPROVED;

        $collection = $this->_customerCollection->create()->getCollection()
//                ->addAttributeToFilter('default_billing', ['notnull' => true])
                ->addAttributeToFilter(self::ATTR_RETAILER_STATUS, ['eq' => $status])
                ->addAttributeToSelect(\AJH\D2R\Helper\Address::ADDRESS_FIELD_WEBSITE)
                ->addAttributeToSelect(\AJH\D2R\Helper\Address::ADDRESS_FIELD_LATLNG)
                ->addFieldToFilter('website_id', $websiteId)
                ->joinAttribute('firstname', 'customer_address/firstname', 'default_billing')
                ->joinAttribute('lastname', 'customer_address/lastname', 'default_billing')
                ->joinAttribute('company', 'customer_address/company', 'default_billing')
                ->joinAttribute('street', 'customer_address/street', 'default_billing')
                ->joinAttribute('city', 'customer_address/city', 'default_billing')
                ->joinAttribute('postcode', 'customer_address/postcode', 'default_billing')
                ->joinAttribute('region', 'customer_address/region', 'default_billing')
                ->joinAttribute('region_id', 'customer_address/region_id', 'default_billing')
                ->joinAttribute('country_id', 'customer_address/country_id', 'default_billing')
                ->joinAttribute('telephone', 'customer_address/telephone', 'default_billing')
                ->joinAttribute('fax', 'customer_address/fax', 'default_billing');
//                ->joinAttribute(\AJH\D2R\Helper\Address::ADDRESS_FIELD_LATLNG, 'customer_address/latlng', 'default_billing');   

        foreach ($collection as $item) {
            $street = explode("\n", $item['street']);
            $country = $this->_country->load($item['country_id']);
            $res[] = [
                'id' => $item['entity_id'],
                'company' => htmlspecialchars($item['company']),
                'street1' => htmlspecialchars($street[0]),
                'street2' => isset($street[1]) ? htmlspecialchars($street[1]) : '',
                'city' => htmlspecialchars($item['city']),
                'postcode' => htmlspecialchars($item['postcode']),
                'state' => htmlspecialchars($item['region']),
                'country' => htmlspecialchars($country->getData('iso3_code')),
                'telephone' => htmlspecialchars($item['telephone']),
                'fax' => htmlspecialchars($item['fax']),
                'website' => htmlspecialchars($item['website']),
                'email' => htmlspecialchars($item['email']),
                'latlng' => $item[\AJH\D2R\Helper\Address::ADDRESS_FIELD_LATLNG],
            ];
        }
                
        return $res;
    }

}
