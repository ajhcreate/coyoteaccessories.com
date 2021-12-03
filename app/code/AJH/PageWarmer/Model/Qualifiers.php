<?php

namespace AJH\PageWarmer\Model;

use AJH\PageWarmer\Model\QualifiersFactory as FitmentQualifiersCollection;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;

class Qualifiers extends \Magento\Framework\Model\AbstractModel {

    protected $_fitmentQualifiersCollection;

    public function __construct(Context $context, Registry $registry,
            FitmentQualifiersCollection $fitmentQualifiersCollection
    ) {
        parent::__construct($context, $registry);

        $this->_fitmentQualifiersCollection = $fitmentQualifiersCollection;
    }

    protected function _construct() {
        $this->_init('AJH\PageWarmer\Model\ResourceModel\Qualifiers');
    }

}
