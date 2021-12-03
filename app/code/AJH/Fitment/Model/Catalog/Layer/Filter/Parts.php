<?php

namespace AJH\Fitment\Model\Catalog\Layer\Filter;

class Parts extends \Magento\Catalog\Model\Layer\Filter\AbstractFilter {
    
    const FILTER_ON_SALE = 1;
    const FILTER_NOT_ON_SALE = 2;
    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->_requestVar = 'sale';
    }
   
    /**
     * Get filter name
     *
     * @return string
     */
    public function getName()
    {
        return Mage::helper('inchoo_sale')->__('Sale');
    }
    /**
     * Get data array for building sale filter items
     *
     * @return array
     */
    protected function _getItemsData()
    {
        $data = array();
        $status = $this->_getCount();
        $data[] = array(
            'label' => Mage::helper('inchoo_sale')->__('On Sale'),
            'value' => self::FILTER_ON_SALE,
            'count' => $status['yes'],
        );
        $data[] = array(
            'label' => Mage::helper('inchoo_sale')->__('Not On Sale'),
            'value' => self::FILTER_NOT_ON_SALE,
            'count' => $status['no'],
        );
        return $data;
    }
    protected function _getCount()
    {
        // Clone the select
    	$select = clone $this->getLayer()->getProductCollection()->getSelect();
        /* @var $select Zend_Db_Select */
	$select->reset(Zend_Db_Select::ORDER);
        $select->reset(Zend_Db_Select::LIMIT_COUNT);
        $select->reset(Zend_Db_Select::LIMIT_OFFSET);
        $select->reset(Zend_Db_Select::WHERE);
        // Count the on sale and not on sale
        $sql = 'SELECT IF(final_price >= price, "no", "yes") as on_sale, COUNT(*) as count from ('
                .$select->__toString().') AS q GROUP BY on_sale';
        $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
        /* @var $connection Zend_Db_Adapter_Abstract */
        return $connection->fetchPairs($sql);
    }
}