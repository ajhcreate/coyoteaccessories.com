<?php
/**
 * @category    Ajh
 * @package     AJH_LabelImages
 * @copyright   Copyright (c) 2018-2019
 */

class AJH_ProductVehicle_Model_Adminhtml_System_Config_Source_Productclass
{
    public function toOptionArray()
    {
        $attribute = Mage::getSingleton('eav/config')
            ->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'web_subclass');

        if ($attribute->usesSource()) {
            return $attribute->getSource()->getAllOptions(true);
        }
    }
}