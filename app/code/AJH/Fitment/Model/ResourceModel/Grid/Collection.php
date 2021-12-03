<?php

namespace AJH\Fitment\Model\ResourceModel\Grid;

use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;

class Collection extends SearchResult {

    /**
     * Override _initSelect to add custom columns
     *
     * @return void
     */
    protected function _initSelect() {
//        $this->addFilterToMap('id', 'main_table.api_fitment');
//        $this->addFilterToMap('name', 'devgridname.value');
        parent::_initSelect();
    }

}
