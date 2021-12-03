<?php

namespace AJH\Fitment\Model\Cache\ResourceModel;

class Models extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {

    public function __construct(\Magento\Framework\Model\ResourceModel\Db\Context $context) {
        parent::__construct($context);
    }

    protected function _construct() {        
        $this->_init('fitment_cache_models', 'id');
    }

}
