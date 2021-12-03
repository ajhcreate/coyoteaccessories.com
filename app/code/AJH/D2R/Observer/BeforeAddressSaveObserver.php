<?php

namespace AJH\D2R\Observer;

use AJH\D2R\Helper\Google\Geocoding;
use Magento\Framework\Event\ObserverInterface;

class BeforeAddressSaveObserver implements ObserverInterface {

    protected $_geocoding;

    public function __construct(Geocoding $geocoding) {
        $this->_geocoding = $geocoding;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
        $latLng = $this->_geocoding->getCoordinates($observer->getDataObject());

        if (is_array($latLng)) {
            $observer->getDataObject()->setData(\AJH\D2R\Helper\Address::ADDRESS_FIELD_LATLNG, implode(',', $latLng));
        } else {
            $observer->getDataObject()->setData(\AJH\D2R\Helper\Address::ADDRESS_FIELD_LATLNG, '');
        }
    }

}
