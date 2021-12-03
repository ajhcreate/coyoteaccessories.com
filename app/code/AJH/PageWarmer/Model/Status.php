<?php

namespace AJH\PageWarmer\Model;

use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use AJH\PageWarmer\Model\YearsFactory as FitmentYears;
use AJH\Fitment\Model\Fitment\Api as FitmentApi;

class Status extends \Magento\Framework\Model\AbstractModel {

    public function __construct(
    Context $context, Registry $registry, FitmentYears $fitmentYears,
            FitmentApi $fitmentApi
    ) {
        parent::__construct($context, $registry);

        $this->_fitmentYears = $fitmentYears;
        $this->fitmentApi = $fitmentApi;
    }

    protected function _construct() {
        $this->_init('AJH\PageWarmer\Model\ResourceModel\Status');
    }

}
