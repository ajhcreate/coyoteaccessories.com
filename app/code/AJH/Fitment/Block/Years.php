<?php

namespace AJH\Fitment\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

use AJH\Fitment\Model\Fitment;

class Years extends Template {

    private $fitment;        

    public function __construct(Context $context, Fitment $fitment) {                         
        $this->fitment = $fitment;        

        parent::__construct($context);
    }

    public function getYears() {
        die('test');
        return $this->fitment->getYearsCollection();
    }

}
