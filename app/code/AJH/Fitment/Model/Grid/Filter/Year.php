<?php

namespace AJH\Fitment\Model\Grid\Filter;

/** \Magento\Framework\Data\OptionSourceInterface * */
class Year implements \Magento\Framework\Option\ArrayInterface {

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     *
     * @var \AJH\Fitment\Model\YearsCollection $_years
     */
    protected $_years;

    public function __construct(\AJH\Fitment\Model\YearsFactory $years) {        
        $this->_years = $years;
    }

//    public function toOptionArray($addEmpty = true) {
//        
//        
//        $collections = $this->_years->create();
//        $years = $collections->getCollection();
//        $options = [];
//        if ($addEmpty) {
//            $options[] = ['label' => __('-- Year --'), 'value' => ''];
//        }
//        
//        foreach ($years as $year) {
//            $data = $year->getData();
//            $options[] = ['label' => intval($data['year']), 'value' => intval($data['year'])];
//        }
//
//        return $options;
//    }

    public function toOptionArray() {
        $result = [];
        foreach ($this->getOptions() as $value => $label) {
            $result[] = [
                'value' => $value,
                'label' => $label,
            ];
        }

        return $result;
    }

    public function getOptions() {
        
        $collections = $this->_years->create();
        $years = $collections->getCollection();
        $years->setOrder('YearID','DESC');
        
        $options = [];
        
        foreach ($years as $year) {
            $data = $year->getData();
            
            $options[intval($data['YearID'])] = __(intval($data['YearID']));            
        }
        
        return $options;
    }

}
