<?php

namespace AJH\D2R\Model\Source\System;

use Magento\Catalog\Model\CategoryFactory;

class Category extends \AJH\D2R\Model\Source\AbstractSource {

    const TAB = '.   ';

    protected $_categoryFactory;

    public function __construct(CategoryFactory $categoryFactory) {
        $this->_categoryFactory = $categoryFactory;
    }

    public function toOptionArray() {
        $collection = $this->_categoryFactory->create()
                ->getCollection()
                ->addAttributeToSelect('name')
                ->addAttributeToSort('path');

        $res = array();

        foreach ($collection as $category) {
            $res[] = array(
                'value' => $category->getId(),
                'label' => str_repeat(self::TAB, $category->getLevel()) . $category->getName()
            );
        }

        return $res;
    }

    public function getOptionText($value) {
        $category = $this->_categoryFactory->create()->load($value);

        return str_repeat(self::TAB, $category->getLevel()) . $category->getName();
    }

}
