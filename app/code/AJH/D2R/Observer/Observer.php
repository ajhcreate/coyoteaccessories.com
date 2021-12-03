<?php

namespace AJH\D2R\Observer;

use Magento\Framework\Registry;
use Magento\Customer\Model\SessionFactory;
use AJH\D2R\Helper\Google\Geocoding;
use AJH\D2R\Helper\Data as D2RData;
use AJH\D2R\Helper\Email as D2REmail;

class Observer {

    const CUSTOMER_PREVIOUS_RETAILER_STATUS = 'customer_previous_retailer_status';

    protected $_registry, $_session, $_geocoding, $_d2rData;

    public function __construct(Registry $registry, SessionFactory $session, Geocoding $geocoding, D2RData $d2rData, D2REmail $d2rEmail) {
        $this->_registry = $registry;
        $this->_session = $session;
        
        $this->_geocoding = $geocoding;
        $this->_d2rData = $d2rData;
        $this->_d2rEmail = $d2rEmail;
    }

    public function adminhtml_customer_prepare_save($data) {
        $accountData = $data['request']->getParam('account');

        // retailer status
        $retailerStatus = (isset($accountData[AJH\D2R\Helper\Retailer::ATTR_RETAILER_STATUS]) && null !== $accountData[AJH\D2R\Helper\Retailer::ATTR_RETAILER_STATUS]) ? $accountData['retailer_status'] : AJH\D2R\Model\Source\Retailer\Status::NONE;

        $data['customer']->setData(AJH\D2R\Helper\Retailer::ATTR_RETAILER_STATUS, $retailerStatus);

        // retailer_cost
        $data['customer']->setData(AJH\D2R\Helper\Retailer::ATTR_RETAILER_COST, isset($accountData[AJH\D2R\Helper\Retailer::ATTR_RETAILER_COST]) ? $accountData[AJH\D2R\Helper\Retailer::ATTR_RETAILER_COST] : null
        );

        $data['customer']->setData(AJH\D2R\Helper\Retailer::ATTR_RETAILER_RESET_TOOL_USE, isset($accountData[AJH\D2R\Helper\Retailer::ATTR_RETAILER_RESET_TOOL_USE]) ? $accountData[AJH\D2R\Helper\Retailer::ATTR_RETAILER_RESET_TOOL_USE] : null);
        $data['customer']->setData(AJH\D2R\Helper\Retailer::ATTR_RETAILER_PDQ_SERIAL, isset($accountData[AJH\D2R\Helper\Retailer::ATTR_RETAILER_PDQ_SERIAL]) ? $accountData[AJH\D2R\Helper\Retailer::ATTR_RETAILER_PDQ_SERIAL] : null);
        $data['customer']->setData(AJH\D2R\Helper\Retailer::ATTR_RETAILER_OTHER_BRAND, isset($accountData[AJH\D2R\Helper\Retailer::ATTR_RETAILER_OTHER_BRAND]) ? $accountData[AJH\D2R\Helper\Retailer::ATTR_RETAILER_OTHER_BRAND] : null);

        // emailing
        $origData = $data['customer']->getOrigData();

        if (
                isset($origData[AJH\D2R\Helper\Retailer::ATTR_RETAILER_STATUS]) && ($origData[AJH\D2R\Helper\Retailer::ATTR_RETAILER_STATUS] != $retailerStatus)
        ) {
            if (!$this->_registry->registry(self::CUSTOMER_PREVIOUS_RETAILER_STATUS)) {

                $this->_registry->unregister(self::CUSTOMER_PREVIOUS_RETAILER_STATUS);
                $this->_registry->register(self::CUSTOMER_PREVIOUS_RETAILER_STATUS, $origData[AJH\D2R\Helper\Retailer::ATTR_RETAILER_STATUS]);
            }
        }
    }

    public function customer_save_after($data) {
        // saving this value doesn't make much sense, just indicating we need to inform a customer on his status change
        $previousRetailerStatus = $this->_registry->registry(self::CUSTOMER_PREVIOUS_RETAILER_STATUS);
        $customer = $data->getData('customer');

        if (null !== $previousRetailerStatus) {
            $currentRetailerStatus = $customer->getData(AJH\D2R\Helper\Retailer::ATTR_RETAILER_STATUS);

            switch ($currentRetailerStatus) {
                case AJH\D2R\Model\Source\Retailer\Status::APPROVED: // send the "approved" email
                    $this->_d2rEmail->notifyCustomer($customer, null, AJH\D2R\Helper\Email::CUSTOMER_NOTIFICATION_TYPE_RETAILER_APP_APPROVED);
                    break;

                case AJH\D2R\Model\Source\Retailer\Status::DECLINED: // send the "declined" email
                    $this->_d2rEmail->notifyCustomer($customer, null, AJH\D2R\Helper\Email::CUSTOMER_NOTIFICATION_TYPE_RETAILER_APP_DECLINED);
                    break;
            }
        }
    }

    public function core_block_abstract_to_html_before($data) {
        if ($data['block'] instanceof \Magento\Customer\Block\Account\Navigation) {
            $retailerStatus = (int) $this->_session
                            ->getCustomer()
                            ->getData(AJH\D2R\Helper\Retailer::ATTR_RETAILER_STATUS);

            if ($retailerStatus != AJH\D2R\Model\Source\Retailer\Status::NONE) {
                $data['block']->addLink('customer_account_d2r', 'd2r/retailer/home', __('Retailer Home'));
            }
        }
    }

    public function customer_address_save_before($observer) {
        $latLng = $this->_geocoding->getCoordinates($observer->getDataObject());

        if (is_array($latLng)) {
            $observer->getDataObject()->setData(AJH\D2R\Helper\Address::ADDRESS_FIELD_LATLNG, implode(',', $latLng));
        } else {
            $observer->getDataObject()->setData(AJH\D2R\Helper\Address::ADDRESS_FIELD_LATLNG, '');
        }
    }

}
