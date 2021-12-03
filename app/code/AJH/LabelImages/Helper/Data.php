<?php

namespace AJH\LabelImages\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;
use AJH\LabelImages\Model\Adminhtml\System\Config\Source\Packagetypes;

class Data extends AbstractHelper {

    const XPATH_IS_ENABLED = 'labelimages/general/enable';
    const XPATH_ALLOWED_PACKAGES = 'labelimages/general/allowed_packages';
    const XPATH_IMAGE_MAPPING = 'labelimages/general/image_mapping';
    const XPATH_IMAGE_DIRECTORY = 'labelimages/general/image_directory';

    public $attributes = array(
        'web_label_image_one' => 'web_pack_qty_0',
        'web_label_image_two' => 'web_pack_qty_1',
        'web_label_image_three' => 'web_pack_qty_2',
        'web_label_image_four' => 'web_pack_qty_3',
    );
    protected $_scopeConfig, $_packagetypes;

    public function __construct(ScopeConfigInterface $scopeConfig, Packagetypes $packagetypes) {
        $this->_scopeConfig = $scopeConfig;
        $this->_packagetypes = $packagetypes;
    }

    /**
     * Perform check if module is enabled
     * @return bool
     */
    public function isEnabled() {
        return $this->_scopeConfig->getValue(self::XPATH_IS_ENABLED);
    }

    /**
     * Return allowed packages
     * @return array
     */
    public function getAllowedPackages() {
        $allowedPackages = $this->_scopeConfig->getValue(self::XPATH_ALLOWED_PACKAGES);
        $allowedPackages = unserialize($allowedPackages);
        return $allowedPackages;
    }

    /**
     * Return image name mapping
     * @return array
     */
    public function getImageNameMap() {
        $imageNameMap = $this->_scopeConfig->getValue(self::XPATH_IMAGE_MAPPING);
        $imageNameMap = unserialize($imageNameMap);
        return $imageNameMap;
    }

    /**
     * Get Image Directory
     * @return string
     */
    public function getImageDirectory() {
        return $this->_scopeConfig->getValue(self::XPATH_IMAGE_DIRECTORY);
    }

    public function getPackageLabel($packageValue) {
        $options = $this->_packagetypes->toOptionArray();
        foreach ($options as $option) {
            $value = $option['value'];
            $label = $option['label'];
            if ($packageValue == $value) {
                return $label;
            }
        }
        return '';
    }

    public function getFileName($attributeCode, $object) {
        $imageNameMap = $this->getImageNameMap();
        foreach ($imageNameMap as $_imageNameMap) {
            if ($_imageNameMap['package'] == $attributeCode) {
                $filename = $_imageNameMap['image_name_conv'];
                $filename = $this->processFileName($filename, $object);
                return $filename;
            }
        }
        return '';
    }

    public function processFileName($filename, $object) {
        $_filename = explode('-', $filename);
        if (isset($_filename[0]) && $_filename[0] == 'prodAttr') {
            $filename = $object->getData($_filename[1]);
            if (isset($_filename[2])) {
                $filename = $filename . '-' . $_filename[2];
            }
        }
        return $filename;
    }

}
