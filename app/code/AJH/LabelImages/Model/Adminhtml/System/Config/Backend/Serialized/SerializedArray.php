<?php

namespace AJH\LabelImages\Model\Adminhtml\System\Config\Backend\Serialized;

use Magento\Config\Model\Config\Backend\Serialized as BackendSerialized;


class SerializedArray extends BackendSerialized{
    /**
     * Unset array element with '__empty' key
     *
     */
    protected function _beforeSave()
    {
        $value = $this->getValue();
        if (is_array($value)) {
            unset($value['__empty']);
        }
        foreach ($value as $id => $fields) {
            foreach ($fields as $key => $val) {
                if (is_array($val)) {
                    $value[$id][$key] = implode(',', $val);
                }
            }
        }

        $this->setValue($value);
        parent::_beforeSave();
    }

    protected function _afterLoad()
    {
        if (!is_array($this->getValue())) {
            $serializedValue = $this->getValue();
            $unserializedValue = false;
            if (!empty($serializedValue)) {
                try {
                    $unserializedValue = unserialize($serializedValue);
                } catch (\Exception $e) {
                    Mage::logException($e);
                }
            }
            $this->setValue($unserializedValue);
        }
    }
}