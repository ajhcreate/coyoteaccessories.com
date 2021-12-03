<?php

namespace AJH\Fitment\Model\Grid\Filter;

/** \Magento\Framework\Data\OptionSourceInterface * */
class SubModel implements \Magento\Framework\Option\ArrayInterface {

    /**
     *
     * @var \AJH\Fitment\Model\SubModelsCollection $_submodels
     */
    protected $_submodels;

    public function __construct(\AJH\Fitment\Model\SubModelsFactory $submodels) {
        $this->_submodels = $submodels;
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

        $collections = $this->_submodels->create();
        $submodels = $collections->getCollection();
        $submodels->setOrder('SubModelName','ASC');
        $options = [];

        foreach ($submodels as $submodel) {
            $data = $submodel->getData();
            $options[intval($data['SubModelID'])] = __($data['SubModelName']);
        }

        return $options;
    }

}
