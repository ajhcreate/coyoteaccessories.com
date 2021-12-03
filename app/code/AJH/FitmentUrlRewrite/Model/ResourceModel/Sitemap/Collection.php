<?php

namespace AJH\FitmentUrlRewrite\Model\ResourceModel\Sitemap;

use AJH\FitmentUrlRewrite\Model\ResourceModel\Sitemap as ResourceModel;
use AJH\FitmentUrlRewrite\Model\Sitemap as Model;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {

    protected $_idFieldName = 'id';
    protected $_eventPrefix = 'ajh_fitment_sitemap_collection';
    protected $_eventObject = 'sitemap_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct() {
        $this->_init(Model::class, ResourceModel::class);
    }

}
