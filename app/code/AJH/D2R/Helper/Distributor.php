<?php

namespace AJH\D2R\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\StoreManagerInterface;
use AJH\D2R\Helper\Data as D2RHelperData;
use AJH\D2R\Model\DistributorFactory as modelDistributor;

class Distributor extends D2RHelperData {

    const ATTR_DISTRIBUTOR_ID = 'distributor_id';

    protected $_storeManager;
    protected $_modelDistributor;

    public function __construct(StoreManagerInterface $storeManager, modelDistributor $modelDistributor) {
        $this->_storeManager = $storeManager;
        $this->_modelDistributor = $modelDistributor;
    }

    public function getDistributors() {
        $distributors = $this->_modelDistributor->create();

        return $distributors->getCollection();
    }

    public function getDistributorsOptions() {
        $res = [];
        foreach ($this->getDistributors() as $item) {
            $res[(int) $item['ID']] = sprintf('%s, %s, %s, %s', $item['Name'], $item['City'], $item['State'], $item['Country']);
        }
        return $res;
    }    

}
