<?php

namespace AJH\LabelImages\Block\Adminhtml\Product\Attribute\Custom\Image;

use Magento\Framework\UrlInterface;
use AJH\LabelImages\Helper\Data as HelperData;

class Uploader extends \Magento\Backend\Block\Widget {

    protected $_urlInterface, $_helperData;

    /**
     * Constructor
     *
     * @param array $data
     */
    public function __construct(\Magento\Backend\Block\Template\Context $context, UrlInterface $urlInterface, HelperData $helperData, $data) {
        parent::__construct($context);
        $this->setType('file');

        $this->_urlInterface = $urlInterface;
        $this->_helperData = $helperData;
    }

    /**
     * Return element html code
     *
     * @return string
     */
    public function getElementHtml() {
        $html = '';
        if ((string) $this->getValue()) {
            $url = $this->_getUrl();
            if (!preg_match("/^http\:\/\/|https\:\/\//", $url)) {
                $url = $this->_urlInterface->getBaseUrl() . $url;
            }
            $html = '<a href="' . $url . '"'
                    . ' onclick="popWin(\'' . $url . '\',\'preview\',\'top:0,left:0,width=820,height=600,resizable=yes,scrollbars=yes\'); return false;">'
                    . '<img src="' . 'images/fam_page_white.gif' . '" id="' . $this->getHtmlId() . '_image" title="' . $this->getValue() . '"'
                    . ' alt="' . $this->getValue() . '" height="16" width="16" class="small-image-preview v-middle" />'
                    . '</a> ';
        }
        $this->setClass('input-file');
        $html .= parent::getElementHtml();
        $html .= $this->_getDeleteCheckbox();
        return $html;
    }

    /**
     * Return html code of delete checkbox element
     *
     * @return string
     */
    protected function _getDeleteCheckbox() {
        $html = '';
        if ($this->getValue()) {
            $label = __('Delete File');
            $html .= '<span class="delete-image">';
            $html .= '<input type="checkbox"'
                    . ' name="' . parent::getName() . '[delete]" value="1" class="checkbox"'
                    . ' id="' . $this->getHtmlId() . '_delete"' . ($this->getDisabled() ? ' disabled="disabled"' : '')
                    . '/>';
            $html .= '<label for="' . $this->getHtmlId() . '_delete"'
                    . ($this->getDisabled() ? ' class="disabled"' : '') . '> ' . $label . '</label>';
            $html .= $this->_getHiddenInput();
            $html .= '</span>';
        }
        return $html;
    }

    /**
     * Return html code of hidden element
     *
     * @return string
     */
    protected function _getHiddenInput() {
        return '<input type="hidden" name="' . parent::getName() . '[value]" value="' . $this->getValue() . '" />';
    }

    /**
     * Get image preview url
     *
     * @return string
     */
    protected function _getUrl() {
        return $this->_helperData->getImageDirectory() . '/' . $this->getValue();
    }

    /**
     * Return name
     *
     * @return string
     */
    public function getName() {
        return $this->getData('name');
    }

}
