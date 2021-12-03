<?php

namespace AJH\Fitment\Plugin;

class Toolbar {

    protected $_objectManager;
    protected $request;

    public function __construct(
    \Magento\Framework\ObjectManagerInterface $objectmanager,
            \Magento\Framework\App\Request\Http $request
    ) {
        $this->_objectManager = $objectmanager;
        $this->request = $request;
    }

    public function aroundSetCollection(
    \Magento\Catalog\Block\Product\ProductList\Toolbar $subject,
            \Closure $proceed, $collection) {
//        $productSkus = ['8170-8170N-E', '73-7256'];

//        $this->_collection = $collection;
//        $category = $this->_objectManager->get('Magento\Framework\Registry')->registry('current_category');
//        if ($category) {
//            $page = $this->request->getParam('p');
//            if ($page == '') {
//                $page = 1;
//            }
//            $collection->addAttributeToFilter('entity_id', array('in' => array(4010, 4012, 4013, 4014, 4015)));
////                    addAttributeToFilter('sku', array('in' => $productSkus));            
//            $this->_collection->getCurPage();
//            $this->_collection->setCurPage($page);
//            
//            echo $collection->getSelect()->__toString();
//        }
        //else
        //{
        //  $this->_collection->setCurPage($this->getCurrentPage());
        //}
//        $subject->setCollection($collection);
        
//        echo $subject->getTotalNum();
//            echo $collection->getSelect()->__toString();
//        die;
        

        $result = $proceed($collection);
//        $subject->getCollection()->addAttributeToFilter('entity_id', array('in' => array(4010, 4012, 4013, 4014, 4015)));
        return $result;
    }

}
