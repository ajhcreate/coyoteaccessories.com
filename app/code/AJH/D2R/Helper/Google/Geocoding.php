<?php

namespace AJH\D2R\Helper\Google;

use AJH\D2R\Helper\Google\AbstractClass;

class Geocoding extends AbstractClass {

    const API_NAME = 'geocode';
    const API_REQUEST_URL = 'https://maps.googleapis.com/maps/api/geocode/json';
    const DEBUG_MESSAGE_PREFIX = 'Geocoding';
    const DEBUG_MODE_URL_KEY = 'geocoding';

    protected $_countryInformationAcquirer;

    public function __construct(
    \Magento\Directory\Api\CountryInformationAcquirerInterface $countryInformationAcquirer
    ) {
        $this->_countryInformationAcquirer = $countryInformationAcquirer;
    }

    public function getCoordinates($address) {
        if (!is_object($address)) {
            if (is_array($address)) {
                $address = new Varien_Object($address);
            } else {
                return [
                    'error' => true,
                    'message' => 'Address is not an object or array',
                ];
            }
        }
        self::_debug($address->getData(), 'Address');

        if (!$address->getStreet() || !$address->getCity() || !$address->getPostcode() || !$address->getCountry() || !$address->getRegion()
        ) {
            return false;
        }

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $_country = $objectManager->create('\Magento\Directory\Model\Country');

        $country = $_country->loadByCode($address->getCountry());

        $addressLine = '';

        $streetAddr = $address->getStreet();
        if (isset($streetAddr[0])) {
            $addressLine .= $streetAddr[0];
        }

        $addressLine .= ', ' . $address->getCity() . ', '
                . $address->getRegionCode() . ' '
                . $address->getPostcode() . ', '
                . $country->getIso3Code();
        self::_debug($addressLine, 'Address line', 2);

        $data = $this->request([
            'address' => $addressLine,
            'region' => strtolower($address->getCountry()),
                ], [
            'country' => $address->getCountry(),
            'administrative_area' => $address->getRegion(),
        ]);

        if (isset($data['error'])) {
            self::_debug($data, 'Response error: ' . $data['message'], 2);
            return false;
        }

        if (isset($data[0]['geometry']['location'])) {
            $res = [
                'lat' => $data[0]['geometry']['location']['lat'],
                'lng' => $data[0]['geometry']['location']['lng'],
            ];
            self::_debug($res, 'Coordinates');

            return $res;
        }

        return false;
    }

}
