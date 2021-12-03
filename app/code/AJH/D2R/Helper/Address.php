<?php

namespace AJH\D2R\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Model\AddressFactory;

class Address extends AbstractHelper {

    const ADDRESS_FIELD_WEBSITE = 'website';
    const ADDRESS_FIELD_LATLNG = 'retailer_latlng';

    protected $storeManager, $_customerAddress;

    public function __construct(StoreManagerInterface $storeManager, AddressFactory $customerAddress) {
        $this->storeManager = $storeManager;
        $this->_customerAddress = $customerAddress;
    }

    public static function parseGeoCoords($value) {
        $res = explode(',', $value);
        return (count($res) == 2) ? $res : false;
    }

    public function customerAddress() {
        $customer_address = $this->_customerAddress->create();
        return $customer_address;
    }

}
