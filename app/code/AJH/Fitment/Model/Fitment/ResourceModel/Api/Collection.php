<?php
namespace AJH\Fitment\Model\Fitment\ResourceModel\Api;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
	protected $_idFieldName = 'post_id';
	protected $_eventPrefix = 'ajh_fitment_api_collection';
	protected $_eventObject = 'api_collection';

	/**
	 * Define resource model
	 *
	 * @return void
	 */
	protected function _construct()
	{
		$this->_init('AJH\Fitment\Model\Fitment\Api', 'AJH\Fitment\Model\Fitment\ResourceModel\Api');
	}

}