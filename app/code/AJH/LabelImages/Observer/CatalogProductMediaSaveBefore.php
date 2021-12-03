<?php

namespace AJH\LabelImages\Observer;

use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Filesystem\DirectoryList;
use AJH\LabelImages\Helper\Data as LabelImagesHelper;

class CatalogProductMediaSaveBefore implements ObserverInterface {

    protected $_logger, $_dir, $_helper;

    public function __construct(LoggerInterface $logger, DirectoryList $dir, LabelImagesHelper $helper) {
        $this->_logger = $logger;
        $this->_dir = $dir;
        $this->_helper = $helper;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
        $product = $observer->getEvent()->getProduct();
        $images = $observer->getEvent()->getImages();
        $clearImages = array();
        foreach ($images['images'] as &$image) {
            if (!empty($image['removed'])) {
                $clearImages[] = $image['file'];
            }
        }
        if (isset($images['values']) && isset($images['images'])) {
            $imageValue = json_decode($images['values']);
            $labelImageCode = $this->getLabelImageAttributeCodes();
            foreach ($labelImageCode as $_labelImageCode) {
                $_image = $imageValue->$_labelImageCode;
                if (in_array($_image, $clearImages)) {
                    $fileName = $this->_helper()->getFileName($_labelImageCode, $product);
                    if (empty($fileName)) {
                        return;
                    }
                    $imageDirectory = $this->_helper()->getImageDirectory();
                    $baseDirectory = $this->_dir->getRoot();
                    $imagePath = $baseDirectory . '/' . $imageDirectory;
                    $labelValue = explode('.', $_image);
                    $fileExtension = array_pop($labelValue);
                    $fileName = $fileName . '.' . $fileExtension;
                    $imageFilePath = $imagePath . '/' . $fileName;
                    if (file_exists($imagePath . '/' . $fileName)) {
                        if (!unlink($imageFilePath)) {
                            $this->_logger->debug("Error deleting", null, 'labelImages.log');
                        }
                    }
                }
            }
        }
    }

    public function getLabelImageAttributeCodes() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $packagetypes = $objectManager->get('AJH\LabelImages\Model\Adminhtml\System\Config\Source\Packagetypes');

        $labelImageAttributeCodes = $packagetypes->getLabelImageAttribute()->getColumnValues('attribute_code');
        return $labelImageAttributeCodes;
    }

    /**
     * @return AJH_LabelImages_Helper_Data|Mage_Core_Helper_Abstract
     */
    protected function _helper() {
        return $this->_helper;
    }

}
