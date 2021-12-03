<?php

namespace AJH\LabelImages\Model\Product\Attribute\Backend\Custom\Image;

use Magento\Framework\UrlInterface;
use Magento\Framework\Filesystem\DirectoryList;
use AJH\LabelImages\Helper\Data as LabelImagesHelper;
use Magento\Catalog\Model\Product\Media\Config as ProductMediaConfig;

class Uploader extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend {

    protected $_urlInterface, $_dir, $_helper, $_mediaConfig;

    public function __construct(UrlInterface $urlInterface, DirectoryList $dir, LabelImagesHelper $helper, ProductMediaConfig $mediaConfig) {
        $this->_urlInterface = $urlInterface;
        $this->_dir = $dir;
        $this->_helper = $helper;

        $this->_mediaConfig = $mediaConfig;
    }

    /**
     * After attribute is saved upload file to media
     * folder and save it to its associated product.
     *
     * @param  Mage_Catalog_Model_Product $object
     * @return AJH_LabelImages_Model_Product_Attribute_Backend_Custom_Image_Uploader
     */
    public function afterSave($object) {
        $attributeName = $this->getAttribute()->getName();
        $value = $object->getData($attributeName);
        if ($value == 'no_selection') {
            $object->setData($this->getAttribute()->getName(), 'no_selection');
            $this->getAttribute()->getEntity()
                    ->saveAttribute($object, $this->getAttribute()->getName());
            $fileName = $this->_helper()->getFileName($attributeName, $object);
            if (empty($fileName)) {
                return;
            }
            $imageDirectory = $this->_helper()->getImageDirectory();
            $baseDirectory = $this->_dir->getRoot();
            $imagePath = $baseDirectory . '/' . $imageDirectory;
            $fileExtensions = array('jpg', 'png');
            foreach ($fileExtensions as $fileExtension) {
                $fileName = $fileName . '.' . $fileExtension;
                $imageFilePath = $imagePath . '/' . $fileName;
                if (file_exists($imagePath . '/' . $fileName)) {
                    if (!unlink($imageFilePath)) {
                        Mage::log("Error deleting", null, 'labelImages.log');
                    }
                }
            }
            return;
        }
        $imageUrl = $this->_mediaConfig->getMediaPath($value);
        if (!$imageUrl || !file_exists($imageUrl)) {
            throw new \Exception(__('Image does not exist.'));
        }
        $fileName = $this->_helper()->getFileName($attributeName, $object);
        if (empty($fileName)) {
            return;
        }
        $pathinfo = pathinfo($imageUrl);

        $imgExtensions = array('jpg', 'jpeg', 'gif', 'png');

        return $this;
    }

    /**
     * Retrive media config
     *
     * @return Mage_Catalog_Model_Product_Media_Config
     */
    protected function _getConfig() {
        return $this->_mediaConfig;
    }

    /**
     * @return AJH_LabelImages_Helper_Data|Mage_Core_Helper_Abstract
     */
    protected function _helper() {
        return $this->_helper;
    }

    public function getAllowedPackages() {
        return $this->_helper()->getAllowedPackages();
    }

    public function getImageNameMap() {
        return $this->_helper()->getImageNameMap();
    }

}
