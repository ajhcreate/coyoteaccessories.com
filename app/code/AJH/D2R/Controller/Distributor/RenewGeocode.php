<?php

namespace AJH\D2R\Controller\Distributor;

use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\AddressFactory as CustomerAddress;
use AJH\D2R\Helper\Google\Geocoding as GeocodingHelper;

//use AJH\D2R\Model\DistributorFactory as DistributorModel;

class RenewGeocode extends \Magento\Framework\App\Action\Action {

    protected $_distributorModel, $_geocodingHelper, $_customerAddress;

    public function __construct(Context $context, \AJH\D2R\Model\DistributorFactory $distributorModel, GeocodingHelper $geocodingHelper, CustomerAddress $customerAddress) {

        $this->_distributorModel = $distributorModel;
        $this->_customerAddress = $customerAddress;
        $this->_geocodingHelper = $geocodingHelper;

        parent::__construct($context);
    }

    public function execute() {
        $_coll = $this->_distributorModel->create();
        $coll = $_coll->getCollection();


        foreach ($coll as $distributor) {

            $address = $this->_customerAddress->create();
            $address->setCompany($distributor['Name'])
                    ->setRegion($distributor['State'])
                    ->setCity($distributor['City'])
                    ->setPostcode($distributor['Zip'])
                    ->setStreet($distributor['Address1'])
                    ->setCountry($distributor['Country']);

            $latLng = $this->_geocodingHelper->getCoordinates($address);

            if ($latLng) {
                $distributor
                        ->setData('LatLng', $latLng['lat'] . ',' . $latLng['lng'])
                        ->save();
                echo sprintf('Geographical data populated successfully for: %s', $address->format('company'));
            } else {
                $message = sprintf('Could not get coordinates for this address: %s, data: %s', $address->format('company'), print_r($distributor, true));
//                $message = 'Could not get coordinates for this address';
                echo $message . '<br/>';
            }
        }

        exit;
    }

}
