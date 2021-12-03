<?php

namespace AJH\Fitment\Model\Cache\ResourceModel;

class SubModels extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {

    public function __construct(\Magento\Framework\Model\ResourceModel\Db\Context $context) {
        parent::__construct($context);
    }

    protected function _construct() {        
        $this->_init('fitment_cache_submodels', 'id');
    }

}
