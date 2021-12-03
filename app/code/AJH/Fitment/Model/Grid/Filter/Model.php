<?php

namespace AJH\Fitment\Model\Grid\Filter;

/** \Magento\Framework\Data\OptionSourceInterface * */
class Model implements \Magento\Framework\Option\ArrayInterface {

    /**
     *
     * @var \AJH\Fitment\Model\YearsCollection $_makes
     */
    protected $_models;

    public function __construct(\AJH\Fitment\Model\ModelsFactory $models) {
        $this->_models = $models;
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

        $collections = $this->_models->create();
        $models = $collections->getCollection();
        $models->setOrder('ModelName','ASC');
        
        $options = [];

        foreach ($models as $model) {
            $data = $model->getData();
            $options[intval($data['ModelID'])] = __($data['ModelName']);
        }

        return $options;
    }

}
