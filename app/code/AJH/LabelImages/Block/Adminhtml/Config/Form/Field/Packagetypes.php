<?php

namespace AJH\LabelImages\Block\Adminhtml\Config\Form\Field;

class Packagetypes extends \Magento\Framework\View\Element\Html\Select {

    public function __construct(\Magento\Framework\View\Element\Context $context, array $data = []) {
        parent::__construct($context, $data);
    }

    public function _toHtml() {
        return parent::_toHtml();
    }

    public function setInputName($value) {
        return $this->setName($value);
    }

}
