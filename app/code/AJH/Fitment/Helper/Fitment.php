<?php

namespace AJH\Fitment\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\ResourceConnection;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use AJH\Fitment\Model\Fitment as FitmentModel;

class Fitment extends AbstractHelper {

    public $_ApiKey = 'GSEPXELYCHZLIEIZPSTXSSGWTDCLYZNRYTYSIOIBTLMDGFMXUX';
    protected static $_read = null;
    protected $_resourceConnection, $_storeManager, $_scopeConfig;
    protected $_fitment;

    public function __construct(ResourceConnection $_resourceConnection,
            StoreManagerInterface $storeManager,
            ScopeConfigInterface $scopeConfig, FitmentModel $fitment) {
        $this->_resourceConnection = $_resourceConnection;

        $this->_storeManager = $storeManager;
        $this->_scopeConfig = $scopeConfig;

        $this->_fitment = $fitment;
    }

    public function getParams() {

        $_year = $this->getRequest()->getParam('year');
        $_make = $this->getRequest()->getParam('make');
        $_model = $this->getRequest()->getParam('model');
        $_submodel = $this->getRequest()->getParam('submodel');
        $_criteria = $this->getRequest()->getParam('criteria');
        $_qualifier = $this->getRequest()->getParam('qualifiers');
        $_qualifier2 = $this->getRequest()->getParam('_qualifiers');


        $params = [
            'yearID' => $_year,
            'makeID' => $_make,
            'modelID' => $_model,
            'submodelID' => $_submodel,
            'qualifiers' => [
                $_qualifier,
                $_qualifier2
            ],
            'criteria' => $_criteria
        ];

        return $params;
    }

    public static function getOptionsHtml($options, $title, $default) {
        $res = '';
        foreach ($options as $key => $value) {
            $_default = $key == $default ? 'selected="selected"' : 'data-default="' . $default . '"';
            $res .= sprintf('<option value="%s" data-title="' . $title . '" ' . $_default . '>%s</option>', htmlspecialchars($key), htmlspecialchars($value));
        }
        return $res;
    }

    public static function getPartsTableHtml($parts) {
        $html = "<table border=\"1\" style=\"width: 100%\">";
        foreach ($parts as $index => $part) {
            $_parts = (array) $part;

            if ($index === 0) {
                $html .= "<tr>";
                foreach ($_parts as $key => $_part) {
                    $html .= "<td>";
                    $html .= "<strong>" . $key . "</strong>";
                    $html .= "</td>";
                }
                $html .= "</tr>";
            }

            $html .= "<tr>";
            foreach ($_parts as $key => $_part) {
                $html .= "<td>";
                $html .= $_part;
                $html .= "</td>";
            }
            $html .= "</tr>";
        }

        $html .= "</table>";

        return $html;
    }

    public static function getTableHtml($parts) {
        $html = "<table border=\"1\" style=\"width: 100%\">";
        $html .= "<tr>";
        $html .= "<td>Status</td>";
        $html .= "<td>Message</td>";
        $html .= "<td>PartNumber</td>";
        $html .= "<td>Description</td>";
        $html .= "<td>ImageThumbURL</td>";
        $html .= "<td>ImageDisplayURL</td>";
        $html .= "<td>ImageDetailURL</td>";
        $html .= "</tr>";

        foreach ($parts as $part) {
            $html .= "<tr>";
            $html .= "<td>{$part->Status}</td>";
            $html .= "<td>{$part->Message}</td>";
            $html .= "<td>{$part->PartNumber}</td>";
            $html .= "<td>{$part->Description}</td>";

            if (trim($part->ImageThumbURL) != '') {
                $html .= "<td><img src=\"{$part->ImageThumbURL}\" /></td>";
            } else {
                $html .= "<td>&nbsp;</td>";
            }

            if (trim($part->ImageDisplayURL) != '') {
                $html .= "<td><img src=\"{$part->ImageDisplayURL}\" /></td>";
            } else {
                $html .= "<td>&nbsp;</td>";
            }

            $html .= "<td>{$part->ImageDetailURL}</td>";
            $html .= "</tr>";
        }

        $html .= "</table>";

        return $html;
    }

    public function imageExists($image_url) {

        $response = true;

        // Creating a variable with an URL 
        // to be checked         
        $url = "http://" . preg_replace('#^https?://#', '', rtrim($image_url, '/'));
        // Initializing new session 
        $ch = curl_init($url);
        // Request method is set 
        curl_setopt($ch, CURLOPT_NOBODY, true);
        // Executing cURL session 
        curl_exec($ch);
        // Getting information about HTTP Code 
        $retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Testing for 404 Error 
        if ($retcode != 200) {
            $response = false;
        }

        return $response;
    }

    public function getDbConnection() {
        $dbConnection = $this->_resourceConnection->getConnection('revo');

        return $dbConnection;
    }

    public function setPlaceholderImage($image_url) {
        $image_exists = $this->imageExists($image_url);

        $placeholder_image_url = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product/placeholder/';
        $placeholder_image_url .= $this->_scopeConfig->getValue('catalog/placeholder/alt_image_placeholder');

        $_image_url = "http://" . preg_replace('#^https?://#', '', rtrim($image_url, '/'));

        if ($image_exists) {
            return $_image_url;
        } else {
            return $placeholder_image_url;
        }
    }

}
