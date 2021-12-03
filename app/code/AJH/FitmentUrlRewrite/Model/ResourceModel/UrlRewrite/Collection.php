<?php

namespace AJH\FitmentUrlRewrite\Model\ResourceModel\UrlRewrite;

use AJH\FitmentUrlRewrite\Model\ResourceModel\UrlRewrite as ResourceModel;
use AJH\FitmentUrlRewrite\Model\UrlRewrite as Model;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {

    protected $_idFieldName = 'id';
    protected $_eventPrefix = 'ajh_fitmenturlrewrite_urlrewrite_collection';
    protected $_eventObject = 'urlrewrite_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct() {
        $this->_init(Model::class, ResourceModel::class);
    }

}
