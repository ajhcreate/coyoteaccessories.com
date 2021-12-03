<?php

namespace AJH\Fitment\Model\Grid\Filter;

/** \Magento\Framework\Data\OptionSourceInterface * */
class Make implements \Magento\Framework\Option\ArrayInterface {

    /**
     *
     * @var \AJH\Fitment\Model\YearsCollection $_makes
     */
    protected $_makes;

    public function __construct(\AJH\Fitment\Model\MakesFactory $makes) {
        $this->_makes = $makes;
    }

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

        $collections = $this->_makes->create();
        $makes = $collections->getCollection();
        $makes->setOrder('MakeName','ASC');
        
        $options = [];                        

        foreach ($makes as $make) {
            $data = $make->getData();            
            
            $options[intval($data['MakeID'])] = __($data['MakeName']);
        }                

        return $options;
    }

}
