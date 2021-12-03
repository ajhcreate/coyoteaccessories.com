<?php

namespace AJH\LabelImages\Block\Adminhtml\Catalog\Product\Helper\Form\Gallery;

use Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Gallery\Content as GalleryContent;
use AJH\LabelImages\Model\Adminhtml\System\Config\Source\Packagetypes;
use AJH\LabelImages\Helper\Data as HelperData;

use Magento\Catalog\Model\Product\Media\Config as MediaConfig;
use Magento\Framework\UrlInterface;

use Magento\Framework\Json\Helper\Data as jsonHelper;

class Content extends GalleryContent {

    protected $_packagetypes, $_helperData, $_mediaConfig, $_jsonHelper, $_urlInterface;

    public function __construct(Packagetypes $packagetypes, HelperData $helperData, MediaConfig $mediaConfig, jsonHelper $jsonHelper, UrlInterface $urlInterface) {
        parent::__construct();
        $this->_packagetypes = $packagetypes;
        $this->_helperData = $helperData;
        $this->_mediaConfig = $mediaConfig;
        
        $this->_jsonHelper = $jsonHelper;
        $this->_urlInterface = $urlInterface;

        $this->setTemplate('AJH_LabelImages::catalog/product/helper/gallery.phtml');
    }

    public function getImagesJson() {
        if (is_array($this->getElement()->getValue())) {
            $value = $this->getElement()->getValue();
            if (count($value['images']) > 0) {
                foreach ($value['images'] as &$image) {
                    $image['url'] = $this->_mediaConfig->getMediaUrl($image['file']);
                }
                return $this->_jsonHelper->jsonEncode($value['images']);
            }
        }
        return '[]';
    }

    public function getImagesValuesJson() {
        $values = array();
        foreach ($this->getMediaAttributes() as $attribute) {
            /* @var $attribute Mage_Eav_Model_Entity_Attribute */
            $values[$attribute->getAttributeCode()] = $this->getElement()->getDataObject()->getData(
                    $attribute->getAttributeCode()
            );
        }
        return $this->_jsonHelper->jsonEncode($values);
    }

    public function getLabelImageAttributeCodes() {
        $labelImageAttributeCodes = $this->_packagetypes->getLabelImageAttribute()->getColumnValues('attribute_code');
        return $labelImageAttributeCodes;
    }

    public function getLabelImagePreview($labelImageCode) {
        $product = $this->getElement()->getDataObject();
        $labelValue = $product->getData($labelImageCode);
        if ($labelValue != 'no_selection') {
            $url = $this->_getUrl($labelImageCode, $labelValue);
            if (!preg_match("/^http\:\/\/|https\:\/\//", $url)) {
                $url = $this->_urlInterface->getBaseUrl() . $url;
            }
            $html = '<a data-value="' . $labelValue . '" class="labelImagePreview no-display" href="' . $url . '"'
                    . ' onclick="popWin(\'' . $url . '\',\'preview\',\'top:0,left:0,width=820,height=600,resizable=yes,scrollbars=yes\'); return false;">'
                    . '<img src="' . Mage::getDesign()->getSkinUrl('images/fam_page_white.gif') . '" id="' . $this->getHtmlId() . '_image" title="' . $this->getValue() . '"'
                    . ' alt="' . $this->getValue() . '" height="16" width="16" class="small-image-preview v-middle" />'
                    . '</a> ';
            return $html;
        }
        return '';
    }

    /**
     * Get image preview url
     * @param string $labelImageCode
     * @param string $labelValue
     * @return string
     */
    protected function _getUrl($labelImageCode, $labelValue) {
        /** @var AJH_LabelImages_Helper_Data $helper */
        $helper = $this->_helperData;
        $product = $this->getElement()->getDataObject();
        $labelValue = explode('.', $labelValue);
        $fileExtension = array_pop($labelValue);
        $filename = $helper->getFileName($labelImageCode, $product);
        return $helper->getImageDirectory() . '/' . $filename . '.' . $fileExtension;
    }

}
