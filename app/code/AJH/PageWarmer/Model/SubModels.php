<?php

namespace AJH\PageWarmer\Model;

use AJH\PageWarmer\Model\SubModelsFactory as FitmentSubModelsCollection;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;

class SubModels extends \Magento\Framework\Model\AbstractModel {

    protected $_fitmentSubModelsCollection;

    public function __construct(
    Context $context, Registry $registry,
            FitmentSubModelsCollection $fitmentSubModelsCollection
    ) {
        parent::__construct($context, $registry);

        $this->_fitmentSubModelsCollection = $fitmentSubModelsCollection;
    }

    protected function _construct() {
        $this->_init('AJH\PageWarmer\Model\ResourceModel\SubModels');
    }

}
