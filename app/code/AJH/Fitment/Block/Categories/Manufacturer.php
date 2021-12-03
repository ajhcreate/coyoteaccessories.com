<?php

namespace AJH\Fitment\Block\Categories;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;
use AJH\Fitment\Model\Fitment;
use Magento\Framework\View\Element\Template\Context;

class Manufacturer extends Template implements BlockInterface {

    protected $_template = "categories/sidebar.phtml";
    protected $fitment;
    public $year, $make, $model, $fitment_year, $fitment_make;

    public function __construct(Context $context, Fitment $fitment) {
        parent::__construct($context);

        $this->fitment = $fitment;                
        
        $this->fitment_year = $this->fitment->_year;
        $this->fitment_make = $this->fitment->_make;

        if (!$this->fitment_make) {
            $this->fitment_make = $this->fitment->_make;
        }

        $this->fitment_model = $this->fitment->_model;

        if (!$this->fitment_model) {
            $this->fitment_model = $this->fitment->_model;
        }

        $this->fitment_submodel = $this->fitment->_submodel;

        if (!$this->fitment_submodel) {
            $this->fitment_submodel = $this->fitment->_submodel;
        }
    }

    public function getFitment() {

        if ($this->fitment_year) {
            $makes = $make = $this->fitment->getMakes();

            $models = $model = $this->fitment->getModels();
            $submodels = $submodel = $this->fitment->getSubModels();

            if (is_array($makes)) {
                foreach ($makes as $_make) {
                    if ($_make['ID'] == $this->fitment_make) {
                        $make = $_make;
                    }
                }
            }

            if (is_array($models)) {
                foreach ($models as $_model) {
                    if ($_model['ID'] == $this->fitment_model) {
                        $model = $_model;
                    }
                }
            }

            if (is_array($submodels)) {
                foreach ($submodels as $_submodel) {
                    if ($_submodel['ID'] == $this->fitment_submodel) {
                        $submodel = $_submodel;
                    }
                }
            }

            $fitment = array(
                'year' => $this->fitment_year,
                'make' => $make,
                'model' => $model,
                'submodel' => $submodel
            );

            return $fitment;
        } else {
            return;
        }
    }

}
