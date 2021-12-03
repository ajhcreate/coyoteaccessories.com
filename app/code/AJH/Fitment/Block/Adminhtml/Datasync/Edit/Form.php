<?php

namespace AJH\Fitment\Block\Adminhtml\Datasync\Edit;

use Magento\Framework\View\Asset\Repository;
use AJH\Fitment\Model\VehiclePartsFactory as RevoFactory;

class Form extends \Magento\Backend\Block\Widget\Form\Generic {

    protected $_assetRepo;
    protected $_revo;

    public function __construct(
    \Magento\Backend\Block\Template\Context $context,
            \Magento\Framework\Registry $registry,
            \Magento\Framework\Data\FormFactory $formFactory,
            \Magento\Framework\View\Asset\Repository $assetRepo,
            RevoFactory $revo, array $data = []
    ) {

        $this->_revo = $revo;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    protected function _prepareForm() {
        $path = $this->_assetRepo->getUrl("AJH_Fitment::assets/files/vehicleparts.csv");

        $model = $this->_coreRegistry->registry('fitment_data');

        $form = $this->_formFactory->create(
                ['data' => [
                        'id' => 'edit_form',
                        'enctype' => 'multipart/form-data',
                        'action' => $this->getData('action'),
                        'method' => 'post'
                    ]
                ]
        );

        $form->setHtmlIdPrefix('datalocation_');

        $fieldset = $form->addFieldset(
                'base_fieldset', ['legend' => __('Sync Fitment Data '), 'class' => 'fieldset-wide']
        );

//        $importdata_script = $fieldset->addField(
//                'syncdata', 'button', array(
//            'label' => '',
//            'name' => 'synctdata',
//            'value' => 'Start Syncing',
//            'note' => '',
//                )
//        );

//        $importdata_script = $fieldset->addField(
//                'sync', 'submit', [
//            'label' => __('Phone Number'),
//            'title' => __('Phone Number'),
//            'name' => 'phone_number',
//            'after_element_html' => ''
//                ]
//        );
//        
//         $fieldset = $form->addFieldset(
//            'base_fieldset',
//            ['legend' => __('General Information'), 'class' => 'fieldset-wide']
//        );
//
//        $vehicleparts = $this->_revo->create();
//        $collection = $vehicleparts->getCollection();
//        $collection->addFieldToFilter('MakeID', array('eq' => '47'));
//        $collection->getSelect()->limit(100, 0);
//
//
//
//        $importdata_script->setAfterElementHtml("   
//        <button type=\"button\">Start Syncing</button>"
//        );


        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

}
