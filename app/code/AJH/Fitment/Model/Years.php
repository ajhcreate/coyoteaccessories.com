<?php

namespace AJH\Fitment\Model;

use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use AJH\Fitment\Model\YearsFactory as FitmentYears;
use AJH\Fitment\Model\Fitment\Api as FitmentApi;

class Years extends \Magento\Framework\Model\AbstractModel {

    protected $_fitmentYears;
    private $fitmentApi;

    public function __construct(
    Context $context, Registry $registry, FitmentYears $fitmentYears,
            FitmentApi $fitmentApi
    ) {
        parent::__construct($context, $registry);

        $this->_fitmentYears = $fitmentYears;
        $this->fitmentApi = $fitmentApi;
    }

    protected function _construct() {
        $this->_init('AJH\Fitment\Model\ResourceModel\Years');
    }

    protected function _initYears() {
        $years = $this->_fitmentYears->create();
        $collection = $years->getCollection();
//Get Object Manager Instance
//        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
//        $this->_fitmentCache = $objectManager->create('AJH\Fitment\Model\Fitment\Cache');

        if ($collection->count() < 1) {
            $this->_fitmentCache->cacheFitmentYears();
        }
    }

    public function getYears() {
        $years = $this->_fitmentYears->create();
        $collection = $years->getCollection();

//        if ($collection->count() < 1) {
//            $collection = $this->fitmentApi->getYears();
//        }

        return $collection;
    }

}
