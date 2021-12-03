<?php

namespace AJH\Fitment\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use AJH\Fitment\Model\Fitment;
use AJH\Fitment\Model\Cache as FitmentCache;

class Submodels extends Template {

    protected $fitment;
    private $cache;

    public function __construct(Context $context, Fitment $fitment,
            FitmentCache $cache) {
        $this->fitment = $fitment;
        $this->cache = $cache;

        parent::__construct($context);
    }

    public function getSubmodels() {        
        $submodels = $this->getCachedSubModels();
        if (count($submodels)) {
            return $submodels;
        }
        return $this->fitment->getSubModels();
    }    

    public function getCachedSubModels() {
        return $this->cache->getSubModels();
    }

}
