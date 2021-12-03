<?php

namespace AJH\ExtendMageContact\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Data extends AbstractHelper {

    const XPATH_IS_ENABLED = 'extendmagecontact/generalsettings/enable';
    const XPATH_CONTACT_US_CMS_PAGE_URL_KEY = 'extendmagecontact/generalsettings/cms_url_key';
    const XPATH_CONTACT_US_CC_EMAIL = 'extendmagecontact/generalsettings/cc';

    protected $_scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig) {
        $this->_scopeConfig = $scopeConfig;
    }

    /* Perform check if module is enabled
     * @return bool
     */

    public function isEnabled() {
        return $this->_scopeConfig->isSetFlag(self::XPATH_IS_ENABLED);
    }

    /**
     * Return contact us cms page url key
     * @return string
     */
    public function getContactCmsUrlKey() {        
        return $this->_scopeConfig->getValue(self::XPATH_CONTACT_US_CMS_PAGE_URL_KEY, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * Return contact us cc email
     * @return string
     */
    public function getCCEmail() {
        return $this->_scopeConfig->getValue(self::XPATH_CONTACT_US_CC_EMAIL, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);          
    }

}
