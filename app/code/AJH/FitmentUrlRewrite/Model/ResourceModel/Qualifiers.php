<?php

namespace AJH\Fitment\Model\ResourceModel;

class Qualifiers extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {

    public function __construct(\Magento\Framework\Model\ResourceModel\Db\Context $context) {
        parent::__construct($context);
    }

    protected function _construct() {
        $this->_init('api_fitment_qualifiers', 'id');
    }

}