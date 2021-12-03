<?php

namespace AJH\Fitment\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use AJH\Fitment\Model\Fitment;
use AJH\Fitment\Model\Cache as FitmentCache;

class Models extends Template {

    private $fitment;
    private $cache;

    public function __construct(Context $context, Fitment $fitment,
            FitmentCache $cache) {
        $this->fitment = $fitment;
        $this->cache = $cache;

        parent::__construct($context);
    }

    public function getModels() {
        $models = $this->getCachedModels();
        if (count($models)) {
            return $models;
        }
        return $this->fitment->getModels();
    }

    public function getCachedModels() {
        return $this->cache->getModels();
    }

}
