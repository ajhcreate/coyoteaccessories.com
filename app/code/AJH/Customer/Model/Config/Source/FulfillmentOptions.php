<?php

namespace AJH\Customer\Model\Config\Source;

use Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory;
use Magento\Framework\DB\Ddl\Table;

/**
 * Custom Attribute Renderer
 */
class FulfillmentOptions extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource {

    /**
     * @var OptionFactory
     */
    protected $optionFactory;

    /**
     * @param OptionFactory $optionFactory
     */

    /**

     * Get all options
     *
     * @return array
     */
    public function getAllOptions() {
        $this->_options = [
            ['label' => 'Select Options', 'value' => ''],
            ['label' => 'SWKC', 'value' => 'SWKC'],
            ['label' => 'SWCA', 'value' => 'SWCA'],
            ['label' => 'SWPA', 'value' => 'SWPA']
        ];

        return $this->_options;
    }

    /**
     * Retrieve option array
     *
     * @return array
     */
    public function getOptionArray() {
        $_options = [];
        foreach ($this->getAllOptions() as $option) {
            $_options[$option['value']] = $option['label'];
        }
        return $_options;
    }

    /**
     * Get a text for option value
     *
     * @param string|int $value
     * @return string|false
     */
    public function getOptionText($value) {
        $options = $this->getAllOptions();
        foreach ($options as $option) {
            if ($option['value'] == $value) {
                return $option['label'];
            }
        }
        return false;
    }

    /**
     * Retrieve flat column definition
     *
     * @return array
     */
    public function getFlatColumns() {
        $attributeCode = $this->getAttribute()->getAttributeCode();

        return [
            $attributeCode => [
                'unsigned' => false,
                'default' => null,
                'extra' => null,
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_VARCHAR,
                'length' => 1,
                'nullable' => true,
                'comment' => $attributeCode . ' column',
            ],
        ];
    }

    /**
     * Retrieve Indexes(s) for Flat
     *
     * @return array
     */
    public function getFlatIndexes() {
        $indexes = [];

        $index = 'IDX_' . strtoupper($this->getAttribute()->getAttributeCode());
        $indexes[$index] = ['type' => 'index', 'fields' => [$this->getAttribute()->getAttributeCode()]];

        return $indexes;
    }

    /**
     * Retrieve Select For Flat Attribute update
     *
     * @param int $store
     * @return \Magento\Framework\DB\Select|null
     */
    public function getFlatUpdateSelect($store) {
        return $this->_eavAttrEntity->create()->getFlatUpdateSelect($this->getAttribute(), $store);
    }

    /**
     * Get a text for index option value
     *
     * @param  string|int $value
     * @return string|bool
     */
    public function getIndexOptionText($value) {
        switch ($value) {
            case 'SWKC':
                return 'SWKC';
            case 'SWCA':
                return 'SWCA';
            case 'SWPA':
                return 'SWPA';
            case '':
                return '';
        }

        return parent::getIndexOptionText($value);
    }

    /**
     * Add Value Sort To Collection Select
     *
     * @param \Magento\Eav\Model\Entity\Collection\AbstractCollection $collection
     * @param string $dir
     *
     * @return \Magento\Eav\Model\Entity\Attribute\Source\Boolean
     */
    public function addValueSortToCollection($collection,
            $dir = \Magento\Framework\DB\Select::SQL_ASC) {
        $attributeCode = $this->getAttribute()->getAttributeCode();
        $attributeId = $this->getAttribute()->getId();
        $attributeTable = $this->getAttribute()->getBackend()->getTable();
        $linkField = $this->getAttribute()->getEntity()->getLinkField();

        if ($this->getAttribute()->isScopeGlobal()) {
            $tableName = $attributeCode . '_t';
            $collection->getSelect()
                    ->joinLeft(
                            [$tableName => $attributeTable], "e.{$linkField}={$tableName}.{$linkField}"
                            . " AND {$tableName}.attribute_id='{$attributeId}'"
                            . " AND {$tableName}.store_id='0'", []
            );
            $valueExpr = $tableName . '.value';
        } else {
            $valueTable1 = $attributeCode . '_t1';
            $valueTable2 = $attributeCode . '_t2';
            $collection->getSelect()
                    ->joinLeft(
                            [$valueTable1 => $attributeTable], "e.{$linkField}={$valueTable1}.{$linkField}"
                            . " AND {$valueTable1}.attribute_id='{$attributeId}'"
                            . " AND {$valueTable1}.store_id='0'", []
                    )
                    ->joinLeft(
                            [$valueTable2 => $attributeTable], "e.{$linkField}={$valueTable2}.{$linkField}"
                            . " AND {$valueTable2}.attribute_id='{$attributeId}'"
                            . " AND {$valueTable2}.store_id='{$collection->getStoreId()}'", []
            );
            $valueExpr = $collection->getConnection()->getCheckSql(
                    $valueTable2 . '.value_id > 0', $valueTable2 . '.value', $valueTable1 . '.value'
            );
        }

        $collection->getSelect()->order($valueExpr . ' ' . $dir);
        return $this;
    }

}
