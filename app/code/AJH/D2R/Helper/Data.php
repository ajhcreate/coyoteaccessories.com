<?php

namespace AJH\D2R\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Stdlib\DateTime\Timezone;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObjectFactory;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\ResourceConnection;

class Data extends AbstractHelper {

    protected static $_read = null;
    protected $_storeManager, $_customerSession, $_timezone, $_scopeConfig, $_logger;
    protected $_resourceConnection;

    public function __construct(StoreManagerInterface $storeManager,
            CustomerSession $customerSession, Timezone $timezone,
            ScopeConfigInterface $scopeConfig, LoggerInterface $logger,
            ResourceConnection $_resourceConnection) {
        $this->_storeManager = $storeManager;
        $this->_customerSession = $customerSession;

        $this->_resourceConnection = $_resourceConnection;

        $this->_timezone = $timezone;

        $this->_scopeConfig = $scopeConfig;
        $this->_logger = $logger;
    }

    public static function getDbConnection($connName = 'externaldb_database') {

        $dbConnection = $this->_resourceConnection->getConnection($connName);

        return $dbConnection;
    }

    public static function getProtocol() {
        return isset($_SERVER['HTTPS']) && filter_var($_SERVER['HTTPS'], FILTER_VALIDATE_BOOLEAN) ? 'https:' : 'http:';
    }

    public static function value($value, $placeholder = '-') {
        return $value ? htmlspecialchars($value) : $placeholder;
    }

    public static function getDateFormatted($date = null,
            $format = 'Y-m-d H:i:s') {

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $dateTime = $objectManager->get("Magento\Framework\Stdlib\DateTime\DateTime");

        if (null === $date) {
            $date = time();
        } elseif (!is_int($date)) {
            $date = strtotime($date);
        }        

        $localDate = $dateTime->gmtDate(null, null);
                
        return $localDate;
    }

    public static function isCustomerRetailer($customer = null) {
        $customer = $customer instanceof CustomerInterface ?: $this->_customerSession;

        $customerGroupId = $customer->getGroupId();
        $allowedGroups = explode(',', $this->_scopeConfig->getValue('d2r_tpms/general/customer_groups_allowed'));
        if (count($allowedGroups)) {
            return in_array($customerGroupId, $allowedGroups);
        }
        return true;
    }

    public static function jsonArray($data) {
        $res = [];

        foreach ($data as $d) {            
            $d = $d->getData();

            if ($s = json_encode($d)) {
                $res[] = $s;
            } else {
                $this->logger->debug('Invalid data for JSON: ' . print_r($d, true));
            }
        }                
        
        return '[' . implode(',', $res) . ']';
        
    }

    public function isCustomerLoggedIn() {
        return $this->_customerSession->isLoggedIn();
    }

}
