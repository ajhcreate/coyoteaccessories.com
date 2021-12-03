<?php

namespace AJH\D2R\Block\Retailer;

use Magento\Customer\Model\Address as CustomerAddress;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\View\Element\Template\Context;
use Magento\Customer\Helper\Address as CustomerAddressHelper;
use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\App\Cache\Type\Config as ConfigCacheType;
use Magento\Directory\Model\ResourceModel\Region\CollectionFactory as RegionCollectionFactory;
use Magento\Directory\Model\ResourceModel\Country\CollectionFactory as CountryCollectionFactory;
use Magento\Customer\Api\AddressRepositoryInterface as AddressRepository;
use Magento\Customer\Api\Data\AddressInterfaceFactory as AddressDataFactory;
use Magento\Customer\Helper\Session\CurrentCustomer as CurrentCustomer;
use Magento\Framework\Api\DataObjectHelper as DataObjectHelper;

use Magento\Store\Model\StoreManagerInterface;

class Register extends \Magento\Customer\Block\Address\Edit {

    protected $_address, $_customerAddress, $_customerSession, $_customerAddressHelper, $_directoryHelperData, $_countryCollectionFactory, $_regionCollectionFactory;
    protected $_storeManager;

    public function __construct(Context $context, CustomerAddress $customerAddress, CustomerSession $customerSessions, CustomerAddressHelper $customerAddressHelper, DirectoryHelper $directoryHelper, EncoderInterface $jsonEncoder, ConfigCacheType $configCacheType, RegionCollectionFactory $regionCollectionFactory, CountryCollectionFactory $countryCollectionFactory, CustomerSession $customerSession, AddressRepository $addressRepository, AddressDataFactory $addressDataFactory, CurrentCustomer $currentCustomer, DataObjectHelper $dataObjectHelper, StoreManagerInterface $storeManager, array $data = []) {
        parent::__construct($context, $directoryHelper, $jsonEncoder, $configCacheType, $regionCollectionFactory, $countryCollectionFactory, $customerSession, $addressRepository, $addressDataFactory, $currentCustomer, $dataObjectHelper, $data);
        $this->_customerAddress = $addressDataFactory;
        $this->_customerSession = $customerSessions;
        $this->_customerAddressHelper = $customerAddressHelper;
        $this->_directoryHelperData = $directoryHelper;

        $this->_countryCollectionFactory = $countryCollectionFactory;
        $this->_regionCollectionFactory = $regionCollectionFactory;
        
        $this->_storeManager = $storeManager; 

        if ($this->_customerSession->isLoggedIn()) {
            $this->_address = $this->_customerAddress->create();
            $this->_address
                    ->setFirstname($this->getCustomer()->getFirstname())
                    ->setMiddlename($this->getCustomer()->getMiddlename())
                    ->setLastname($this->getCustomer()->getLastname())
                    ->setSuffix($this->getCustomer()->getSuffix())
                    ->setEmail($this->getCustomer()->getEmail());
        }
    }

    public function getAddress() {
        $res = $this->_customerSession->getCustomer()->getDefaultBillingAddress();

        if (!$res) {
            $res = $this->_customerSession->getCustomer()->getDefaultShippingAddress();
        }

        if (!$res) {
            $res = parent::getAddress();
        }

        return $res;
    }

    public function getRegionCollection() {
        $collection = $this->_regionCollectionFactory->create();
        return $collection;
    }

    public function getCountryCollection() {
        $collection = $this->_countryCollectionFactory->create()->loadByStore();
        return $collection;
    }

    public function getRegions() {
        return $options = $this->getRegionCollection()
                ->toOptionArray();
    }

    public function getCountries() {
        return $options = $this->getCountryCollection()
                ->setForegroundCountries($this->getTopDestinations())
                ->toOptionArray();
    }

    public function getRegionSelectHtml() {
        $regions = $this->getRegions();

        $html = '<select id="region_id" name="region_id" title="State/Province" class="required-entry validate-select" style="" defaultvalue="0">';
        $html .= '<option value="">Please select region, state or province</option>';
        foreach ($regions as $region) {
            if($region['country_id']==='US'){
                $html .= "<option value=\"{$region['value']}\">{$region['label']}</option>";
            }
        }
        $html .= '</select>';

        return $html;
    }

    public function getCountrySelectHtml() {
        $countries = $this->getCountries();

        $html = '<select name="country_id" id="country" class="validate-select" title="Country">';
        foreach ($countries as $country) {
            $html .= "<option value=\"{$country['value']}\">{$country['label']}</option>";
        }
        $html .= '</select>';

        return $html;
    }

    protected function _prepareLayout() {
        // do nothing; just overriding the excessive stuff
    }

    public function getSession() {
        return $this->_customerSession;
    }

    public function addressHelper() {
        return $this->_customerAddressHelper;
    }

    public function _getRegions() {
        return $this->_directoryHelperData->getRegionJson();
    }
    
    /**
     * Get current url for store
     *
     * @param bool|string $fromStore Include/Exclude from_store parameter from URL
     * @return string     
     */
    public function getStoreUrl($fromStore = true)
    {
        return $this->_storeManager->getStore()->getCurrentUrl($fromStore);
    }

}
