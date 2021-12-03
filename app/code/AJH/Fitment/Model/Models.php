<?php

namespace AJH\Fitment\Model;

use AJH\Fitment\Model\ModelsFactory as FitmentModelsCollection;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;

class Models extends \Magento\Framework\Model\AbstractModel {

    protected $_fitmentModelsCollection;
    
    public function __construct(
        Context $context, Registry $registry,
        FitmentModelsCollection $fitmentModelsCollection
    ) {
        parent::__construct($context, $registry);

        $this->_fitmentModelsCollection = $fitmentModelsCollection;
    }

    protected function _construct() {
        $this->_init('AJH\Fitment\Model\ResourceModel\Models');
    }

}
