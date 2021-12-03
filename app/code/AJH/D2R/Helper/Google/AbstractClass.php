<?php

namespace AJH\D2R\Helper\Google;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Registry;

class AbstractClass {

    const LOG_FILE_NAME = 'googleAPI.log';
    const DEBUG_MESSAGE_PREFIX = 'Google Maps API';
    const DEBUG_MODE_REGISTRY_NAME = 'GOOGLE_MAPS_API_DEBUG';
    const DEBUG_MODE_URL_KEY = 'debug';
    const API_KEY_URL_NAME = 'key';
    const REQUEST_MAX_COUNT = 3;
    const REQUEST_MIN_TIME = 1000;
    const REQUEST_MAX_TIME = 5000;
    const RESULT_STATUS_OK = 'OK';
    const RESULT_ZERO_RESULTS = 'ZERO_RESULTS';
//	const RESULT_OVER_QUERY_LIMIT = 'OVER_QUERY_LIMIT';
//	const RESULT_REQUEST_DENIED = 'REQUEST_DENIED';
//	const RESULT_INVALID_REQUEST = 'INVALID_REQUEST';
//	const RESULT_UNKNOWN_ERROR = 'UNKNOWN_ERROR';

    const API_NAME = 'maps';
    const API_REQUEST_URL = 'https://maps.googleapis.com/maps';

    protected static $_debugMode = false;
    protected static $_scopeConfig;
    protected static $_registry;

    public function __construct(ScopeConfigInterface $scopeConfig, Registry $registry) {
        self::$_scopeConfig = $scopeConfig;
        self::$_registry = $registry;
    }
    
    protected static function getRegistry(){
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $manager = $om->get('Magento\Framework\Registry'); 
        
        return $manager;
    }
    
    protected static function _scopeConfig(){
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $manager = $om->get('Magento\Framework\App\Config\ScopeConfigInterface'); 
        
        return $manager;
    }

    public function composeURL($params, $components = []) {
        if (!is_array($params)) {
            $params = [$params];
        }

        $paramsEncoded = [];
        foreach ($params as $name => $value) {
            if (!is_numeric($name)) {
                $paramsEncoded[] = urlencode($name) . '=' . urlencode($value);
            } else {
                $paramsEncoded[] = urlencode($value);
            }
        }

        if (count($components)) {
            $encodedComponents = [];
            foreach ($components as $name => $value) {
                $encodedComponents[] = urlencode($name) . ':' . urlencode($value);
            }
            $paramsEncoded['components'] = implode('|', $components);
        }

        $paramsEncoded[static::API_KEY_URL_NAME] = self::_scopeConfig()->getValue('googleAPI/' . static::API_NAME . '/api_key');

        return static::API_REQUEST_URL . '?' . implode('&', $paramsEncoded);
    }

    public function request($params) {
        $url = $this->composeURL($params);
        self::_debug($url, 'URL');

        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => $url,
//			CURLOPT_VERBOSE => 0,
            CURLOPT_RETURNTRANSFER => 1,
//			CURLOPT_SSLVERSION => 3,
//			CURLOPT_SSL_VERIFYPEER => false,
//			CURLOPT_SSL_VERIFYHOST => 2,
        ));

        $error = false;

        for ($retry = 1; $retry <= static::REQUEST_MAX_COUNT; $retry++) {
            try {
                $response = curl_exec($ch);
                self::_debug($response, 'Response');

                if (false === $response) {
                    $error = curl_error($ch);
                }

                curl_close($ch);

                if ($error) {
                    throw new \Exception('CURL: ' . $error);
                }

                $data = json_decode($response, true);
                self::_debug($data, 'Response decoded', 2);

                if (null === $data) {
                    throw new \Exception('Request: cannot decode response JSON, response: ' . $response);
                }

                if (!isset($data['status'])) {
                    throw new \Exception('Request: no status field in response JSON, response: ' . $response);
                }

                switch ($data['status']) {
                    case self::RESULT_STATUS_OK:
                        return $data['results'];

                    case self::RESULT_ZERO_RESULTS:
                        return [
                            'error' => true,
                            'message' => 'Zero results',
                        ];

                    default:
                        throw new \Exception($data['status']);
                }
            } catch (\Exception $e) {
                if ($retry == static::REQUEST_MAX_COUNT) { // if this is the last retry
                    self::_debug(null, $e->getMessage(), '*');
                    return [
                        'error' => true,
                        'try' => $retry,
                        'message' => $e->getMessage(),
                        'url' => $url,
                    ];
                }

                // otherwise, just try again later
                $sleepTime = mt_rand(static::REQUEST_MIN_TIME, static::REQUEST_MAX_TIME);
                usleep($sleepTime);
            }
        }
        return false;
    }

    protected static function _debug($var, $note = null, $mode = '') {
        
        
        
        if (!self::$_debugMode) {
            self::$_debugMode = self::getRegistry()->registry(static::DEBUG_MODE_REGISTRY_NAME);
            if (!self::$_debugMode) {
                self::$_debugMode = @$_GET[static::DEBUG_MODE_URL_KEY];
            }
        }

        if (!self::$_debugMode)
            return;

        if (self::$_debugMode == 'a' || self::$_debugMode == 'all' || (self::$_debugMode == $mode) || ($mode == '*')
        ) {
            echo '<div style="margin:20px !important;height:auto !important;width:auto !important;'
            . 'background-color:#fff !important; color: #000 !important;overflow:auto;">'
            . static::DEBUG_MESSAGE_PREFIX . ' ' . htmlspecialchars($note) . '<br/>';
            vd($var);
            echo '</div>';
        }
    }

}
