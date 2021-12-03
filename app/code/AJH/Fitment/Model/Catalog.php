<?php
namespace AJH\Fitment\Model;

class Catalog extends \Magento\Framework\Model\AbstractModel{
    public function _construct()
    {
        $this->_init('fitment/catalog');
    }
}