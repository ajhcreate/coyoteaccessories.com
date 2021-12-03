<?php

namespace AJH\LabelImages\Model\Adminhtml\System\Config\Source;

class Packagetypes {

    /**
     * @var null|Mage_Eav_Model_Resource_Entity_Attribute_Collection
     */
    protected $collection = null;
    protected $_option = array();        

    /**
     * @return Mage_Eav_Model_Resource_Entity_Attribute_Collection|null
     */
    public function getLabelImageAttribute() {
        
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $eavAttribute = $objectManager->get('Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection');
        
        if (is_null($this->collection)) {
            $this->collection = $eavAttribute
                    ->setEntityTypeFilter(4)
                    ->setFrontendInputTypeFilter('media_image')
                    ->addFieldToFilter('attribute_code', array('like' => '%web%'))
                    ->addFieldToSelect('attribute_code')
                    ->addFieldToSelect('frontend_label');
        }
        
        return $this->collection;
    }

    public function toOptionArray() {
        if (empty($this->_option)) {
            foreach ($this->getLabelImageAttribute() as $attribute) {
                $this->_option[] = array(
                    'value' => $attribute->getAttributeCode(),
                    'label' => __($attribute->getFrontendLabel())
                );
            }
        }
        
        return $this->_option;
    }

}
