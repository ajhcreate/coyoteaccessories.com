<?php

namespace AJH\LabelImages\Block\Adminhtml\System\Config\Frontend;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;

class Imagemapping extends AbstractFieldArray {

    /**
     * @var
     */
    protected $_itemRenderer, $_packagetypes;

    public function _prepareToRender() {
        $this->addColumn('package', array(
            'label' => __('Label System Image Attribute'),
            'style' => 'width:100px',
            'class' => 'package_type'
        ));
        $this->addColumn('image_name_conv', array(
            'label' => __('Image Name Convention'),
            'style' => 'width:300px',
        ));

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
//        $this->setTemplate('ajh/labelimages/system/config/frontend/imagemapping.phtml');
    }

    /**
     * Get select block for type
     *
     */
    protected function _getTypeRenderer() {
        if (!$this->_itemRenderer) {
            $this->_itemRenderer = $this->getLayout()
                    ->createBlock('\AJH\LabelImages\Block\Adminhtml\Config\Form\Field\Packagetypes')
                    ->setIsRenderToJsTemplate(true);
        }
        return $this->_itemRenderer;
    }

    /**
     * @param string $columnName
     * @throws Exception
     */
    public function renderCellTemplate($columnName) {

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $packagetypes = $objectManager->get('AJH\LabelImages\Model\Adminhtml\System\Config\Source\Packagetypes');

        $inputName = $this->getElement()->getName() . '[#{_id}][' . $columnName . ']';

        if ($columnName == "package") {
            $options = $packagetypes->toOptionArray();

            return $this->_getTypeRenderer()
                            ->setName($inputName)
                            ->setTitle($columnName)
                            ->setExtraParams('style="width:200px"')
                            ->setClass('required-entry')
                            ->setOptions($options)
                            ->toHtml();
        }

        return parent::renderCellTemplate($columnName);
    }

    /**
     * Assign extra parameters to row
     *
     * @param Varien_Object $row
     */
    protected function _prepareArrayRow(DataObject $row) {
        parent::__construct();

        $row->setData(
                'option_extra_attr_' . $this->_getTypeRenderer()->calcOptionHash(
                        $row->getData('package')), 'selected="selected"'
        );
    }

    protected function _toHtml() {
        $html = parent::_toHtml();
        $html .= '<script type="text/javascript">
                       //<![CDATA[
                       document.observe("dom:loaded", function() {
                            var enabled = $(\'ajh_labelimages_general_enable\');
                            var mapping = $(\'row_ajh_labelimages_general_image_mapping\');
                            showHideMapping();
                            enabled.observe(\'change\', function(event) {
                            console.log("CHANGE")
                                showHideMapping();
                            });
                          
                            function showHideMapping() {
                                var isEnabled = enabled.value;
                                console.log(isEnabled);
                                //var hasPackage = getSelectValues(allowedPackages).length;
                                if (isEnabled == 1) {
                                    mapping.show();
                                } else {
                                    mapping.hide();
                                }
                            }
                        });
                     //]]>
                    </script>';
        return $html;
    }

}
