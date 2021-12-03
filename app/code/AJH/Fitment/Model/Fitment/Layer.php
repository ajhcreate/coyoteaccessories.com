<?php
namespace AJH\Fitment\Model\Fitment;

class Layer extends \Magento\Catalog\Model\Layer {

    protected $_productCollections = array();

    public function prepareProductCollection($collection) {
        $collection
                ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
                ->addMinimalPrice()
                ->addFinalPrice()
                ->addTaxPercents()
                ->addUrlRewrite($this->getCurrentCategory()->getId());

        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);

        return $this;
    }

    public function getProductCollection() {
        $params = Mage::app()->getRequest()->getParams();
        $categoryId = Mage::app()->getRequest()->getParam('cat');

        $current_category = Mage::getModel('catalog/category')->load($categoryId);

        $model = Mage::getModel('fitment/fitment_api');
        $_skus = $model->retrieveVehicleParts(
                $model->getFitmentYearID(), $model->getFitmentMakeID(), $model->getFitmentModelID(), $model->getFitmentSubModelID(), $model->getFitmentQualifiers(), $model->getFitmentQualifiers2()
        );

        if (isset($this->_productCollections[$categoryId])) {
            $collection = $this->_productCollections[$categoryId];
        } else {
//            $collection = Mage::getModel('catalog/product')->getCollection()->addAttributeToSelect('*');
//            $collection->addAttributeToFilter('status', 1)
//                    ->addAttributeToFilter('sku', array('in' => $_skus))
//                    ->addFieldToFilter('visibility', Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH);
//
//            if ($categoryId):
//                $collection->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id=entity_id', null, 'left')
//                        ->addAttributeToFilter('category_id', array('in' => array($categoryId)));
//            endif;
//
//            foreach ($params as $code => $param) {
//                $entity = 'catalog_product';
//                $attr = Mage::getResourceModel('catalog/eav_attribute')
//                        ->loadByCode($entity, $code);
//                if ($attr->getId()) {
//                    $collection->addAttributeToFilter($code, array('eq' => $param));
//                }
//            }

            $collection = $current_category->getProductCollection();
            $collection->addAttributeToFilter('status', 1)
                    ->addAttributeToFilter('sku', array('in' => $_skus))
                    ->addFieldToFilter('visibility', Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH);

            foreach ($params as $code => $param) {
                $entity = 'catalog_product';
                $attr = Mage::getResourceModel('catalog/eav_attribute')
                        ->loadByCode($entity, $code);
                if ($attr->getId()) {
                    $collection->addAttributeToFilter($code, array('eq' => $param));
                }
            }

            $this->prepareProductCollection($collection);

            $this->prepareProductCollection($collection);
            $this->_productCollections[$categoryId] = $collection;


//            $collection = Mage::getModel('fitment/fitment_api')->getVehicleParts();
        }

        return $collection;
    }

}
