<?php

namespace AJH\D2R\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Help extends AbstractDb {

    public function __construct(\Magento\Framework\Model\ResourceModel\Db\Context $context, $connectionName = "revo") {
        parent::__construct($context, $connectionName);
    }

    public function _construct() {
        $this->_init('tpmschallengeworksheet', 'ID');
    }

}
