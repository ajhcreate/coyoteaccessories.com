<?php

namespace AJH\D2R\Model\Source;

class AbstractSource extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource {

    protected $_shortOptions = null;

    public function getAllOptions($withEmpty = false) {
        if (is_null($this->_options)) {
            $this->_options = $this->toOptionArray();
        }

        if ($withEmpty) {
            $options = $this->_options;
            array_unshift($options, array('label' => '', 'value' => ''));

            return $options;
        }

        return $this->_options;
    }

    public function getOptionArray() {
        $res = array();

        foreach ($this->getAllOptions() as $option) {
            $res[$option['value']] = $option['label'];
        }

        return $res;
    }

    public function getOptionText($value) {
        $options = $this->getAllOptions();

        foreach ($options as $option) {
            if ($option['value'] == $value) {
                return $option['label'];
            }
        }
        return false;
    }

    public function toShortOptionArray() {
        return [];
    }

    public function toOptionArray() {
        $res = array();

        foreach ($this->toShortOptionArray() as $key => $value)
            $res[] = array(
                'value' => $key,
                'label' => $value
            );

        return $res;
    }

    public function getLabel($value) {
        return $this->getOptionText($value);
    }

}
