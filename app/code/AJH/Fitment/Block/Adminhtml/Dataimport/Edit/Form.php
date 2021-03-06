<?php

namespace AJH\Fitment\Block\Adminhtml\Dataimport\Edit;

use Magento\Framework\View\Asset\Repository; 

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    protected $_assetRepo;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        array $data = []
    ) {   
        parent::__construct($context, $registry, $formFactory, $data);
    }


    protected function _prepareForm()
    {   
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
                'base_fieldset',
                ['legend' => __('Import Fitment Data '), 'class' => 'fieldset-wide']
            );

       $importdata_script =  $fieldset->addField(
            'importdata',
            'file', 
                array(
                        'label'     => 'Upload File',
                        'required'  => true,
                        'name'      => 'importdata',  
                        'note' => 'Allow File type: .csv and .xls', 
                     )
        );

        $importdata_script->setAfterElementHtml("   

        <span id='sample-file-span' ><a id='sample-file-link' target='_blank' href='".$path."'  >Download File Format</a></span>

            <script type=\"text/javascript\">

            document.getElementById('fitment_importdata').onchange = function () { 

                var fileInput = document.getElementById('fitment_importdata');

                var filePath = fileInput.value;

                var allowedExtensions = /(\.csv|\.xls)$/i; 

                if(!allowedExtensions.exec(filePath)){
                    alert('Please upload file having extensions .csv or .xls only.');
                    fileInput.value = '';
                }

            };

            </script>"
        );

        
        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

}