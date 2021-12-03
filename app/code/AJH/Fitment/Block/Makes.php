<?php

namespace AJH\Fitment\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use AJH\Fitment\Model\Fitment;
use AJH\Fitment\Model\Cache as FitmentCache;

class Makes extends Template {

    private $fitment;
    private $cache;

    public function __construct(Context $context, Fitment $fitment,
            FitmentCache $cache) {
        $this->fitment = $fitment;
        $this->cache = $cache;
        
        parent::__construct($context);
    }

    public function getMakes() {
        $makes = $this->getCachedMakes();
        if(count($makes)){
            return $makes;
        }
        return $this->fitment->getMakes();
    }
    
    public function getCachedMakes() {
        return $this->cache->getMakes();
    }

}
