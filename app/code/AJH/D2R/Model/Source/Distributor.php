<?php

namespace AJH\D2R\Model\Source;

use AJH\D2R\Model\DistributorFactory;

class Distributor extends \AJH\D2R\Model\Source\AbstractSource {

    protected $_distributorFactory;

    public function __construct(DistributorFactory $distributorFactory) {
        $this->_distributorFactory = $distributorFactory;
    }

    public function toShortOptionArray() {
        if (null === $this->_shortOptions) {
            $this->_shortOptions = $this->_distributorFactory->create()->getCollection()
                    ->toOptionArray();
        }

        return $this->_shortOptions;
    }

}
