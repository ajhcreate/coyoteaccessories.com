<?php

namespace AJH\ProductVehicle\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Filter\FilterManager;
use Magento\Framework\Stdlib\StringUtils;
use Magento\Framework\Registry as FrameworkRegistry;
use Magento\Catalog\Model\Product as CatalogProduct;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Escaper as Escaper;

use Magento\Framework\App\Action\Context;

class Data extends AbstractHelper {

    const XPATH_ENABLED = 'vehicle/general/enable';
    const XPATH_ENABLE_SEARCH = 'vehicle/general/enable_search';
    const XPATH_MIN_QUERY_LENGTH = 'vehicle/general/min_query_length';
    const XPATH_MAX_QUERY_LENGTH = 'vehicle/general/max_query_length';
    const XPATH_NO_RESULT_TEXT = 'vehicle/general/no_result_text';
    const XPATH_LOGIN_ONLY = 'vehicle/general/login_only';

    /**
     * Query variable name
     */
    const QUERY_VAR_NAME = 'partnumber';

    /**
     * Query string
     *
     * @var string
     */
    protected $_queryText;

    /**
     * Is a maximum length cut
     *
     * @var bool
     */
    protected $_isMaxLength = false;

    /**
     * Retrieve search query parameter name
     *
     * @return string
     */
    protected $_storeManager, $_scopeConfig, $_logger, $_filterManager, $_registry, $_catalogProducts, $_cusomterSession, $_escaper, $_request;

    public function __construct(Context $context, StoreManagerInterface $storeManager, ScopeConfigInterface $scopeConfig, LoggerInterface $logger, StringUtils $filterManager, FrameworkRegistry $registry, CatalogProduct $catalogProduct, CustomerSession $cusomterSession, Escaper $escaper) {
        $this->_storeManager = $storeManager;
        $this->_scopeConfig = $scopeConfig;
        $this->_logger = $logger;

        $this->_filterManager = $filterManager;
        $this->_registry = $registry;

        $this->_catalogProducts = $catalogProduct;
        $this->_cusomterSession = $cusomterSession;

        $this->_escaper = $escaper;

        $this->_request = $context->getRequest();
    }

    public function getRequest() {
        return $this->_request;
    }

    /**
     * Perform check if module is enabled
     * @return bool
     */
    public function isEnabled() {
        return $this->_scopeConfig->getValue(self::XPATH_ENABLED, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * Perform check if part search is enabled
     * @return bool
     */
    public function isSearchEnabled() {
        $isLoginOnly = $this->isLoginOnly();
        if ($isLoginOnly) {
            if ($this->_cusomterSession->isLoggedIn()) {
                return $this->_scopeConfig->getValue(self::XPATH_ENABLE_SEARCH, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            } else {
                return false;
            }
        }
        return $this->_scopeConfig->getValue(self::XPATH_ENABLE_SEARCH, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * Perform check if to only allow search by part number to login users only
     * @return bool
     */
    public function isLoginOnly() {
        return $this->_scopeConfig->getValue(self::XPATH_LOGIN_ONLY, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * Return no result text
     * @return string
     */
    public function getNoResultText() {
        return $this->_scopeConfig->getValue(self::XPATH_NO_RESULT_TEXT, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function debug($message) {
        $this->_logger->debug($message);
    }

    public function getQueryParamName() {
        return self::QUERY_VAR_NAME;
    }

    /**
     * Retrieve minimum query length
     *
     * @param mixed $store
     * @return int|string
     */
    public function getMinQueryLength($store = null) {
        return 2; //$this->_scopeConfig->getValue(self::XPATH_MIN_QUERY_LENGTH, $store);
    }

    /**
     * Retrieve maximum query length
     *
     * @param mixed $store
     * @return int|string
     */
    public function getMaxQueryLength($store = null) {
        return 100; //$this->_scopeConfig->getValue(self::XPATH_MAX_QUERY_LENGTH, $store);
    }

    /**
     * Retrieve search query text
     *
     * @return string
     */
    public function getQueryText() {
        if (!isset($this->_queryText)) {
            $this->_queryText = $this->getRequest()->getParam($this->getQueryParamName());
            if ($this->_queryText === null) {
                $this->_queryText = '';
            } else {
                /* @var $stringHelper Mage_Core_Helper_String */
                $stringHelper = $this->_filterManager;
                $this->_queryText = is_array($this->_queryText) ? '' : $stringHelper->cleanString(trim($this->_queryText));

                $maxQueryLength = $this->getMaxQueryLength();
                if ($maxQueryLength !== '' && $stringHelper->strlen($this->_queryText) > $maxQueryLength) {
                    $this->_queryText = $stringHelper->substr($this->_queryText, 0, $maxQueryLength);
                    $this->_isMaxLength = true;
                }
            }
        }
        return $this->_queryText;
    }

    /**
     * Retrieve HTML escaped search query
     *
     * @return string
     */
    public function getEscapedQueryText() {
        return $this->_escaper->escapeHtml($this->getQueryText());
    }

    public function getProductGarage() {
        return $this->_cusomterSession->getProductMyGarage();
    }

    /**
     * Retrieve current product
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct() {
        if (!$this->_registry->registry('product') && $this->getProductId()) {
            $product = $this->_catalogProducts->load($this->getProductId());
            $this->_registry->registry('product', $product);
        }
        return $this->_registry->registry('product');
    }

    public function getProductSku() {
        if ($this->getProduct()) {
            return $this->getProduct()->getSku();
        } elseif ($productSku = $this->getRequest()->getParam('productsku')) {
            return $productSku;
        }
    }

}
