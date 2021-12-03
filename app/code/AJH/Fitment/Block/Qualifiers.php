<?php

namespace AJH\Fitment\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\App\RequestInterface;
use AJH\Fitment\Model\Fitment;
use AJH\Fitment\Model\Cache as FitmentCache;

class Qualifiers extends Template {

    protected $fitment;
    private $cache;
    public $fitment_year;
    public $fitment_make;
    public $fitment_model;
    public $fitment_submodel;
    public $qualifierGroups = ['RegionID' => ['ID' => 'RegionID', 'Name' => 'RegionName', 'Label' => 'Region'], 'DriveTypeID' => ['ID' => 'DriveTypeID', 'Name' => 'DriveTypeName', 'Label' => 'Drive Type']];

    public function __construct(Context $context, Fitment $fitment,
            RequestInterface $request, FitmentCache $cache) {

        $this->fitment = $fitment;
        $this->cache = $cache;

//        $this->fitment_year = $request->getParam('year', false);
//        $this->fitment_make = $request->getParam('make', false);
//        $this->fitment_model = $request->getParam('model', false);
//        $this->fitment_submodel = $request->getParam('submodel', false);


        parent::__construct($context);
    }

    public function getQualifiers() {
        $qualifiers = $this->getCachedQualifiers();
        if (count($qualifiers)) {
            return $qualifiers;
        }

        return $this->fitment->getQualifiers();
    }

    public function getQualifiersSelect() {
        return $this->qualifierGroups;
    }

    public function getQualifiersOptions($_group) {
        $group = $this->qualifierGroups;
        $qualifiers = $this->fitment->getQualifiers();
        $options = '';
        $_options = [];
        foreach ($qualifiers as $key => $qualifier) {
            if ($key === 0) {
                $options .= "<option value=\"\">{$group[$_group]['Label']}</option>";
            }
            $_options[$qualifier[$group[$_group]['ID']]] = "<option value=\"{$qualifier[$group[$_group]['ID']]}\">{$qualifier[$group[$_group]['Name']]}</option>";
        }

        $options .= implode('', $_options);

        return $options;
    }

    public function getCachedQualifiers() {
        return $this->cache->getQualifiers();
    }

}
